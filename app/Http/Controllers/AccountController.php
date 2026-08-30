<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * One page, four Bootstrap tabs (Dashboard/Orders/Downloads/Address/Account
     * Details/Change Password) — everything each tab needs is gathered here
     * rather than per-tab routes, so switching tabs never reloads the page.
     */
    public function index()
    {
        $user = Auth::guard('web')->user();

        $orders = Order::where('user_id', $user->id)->latest()->get();

        // Falls back to the most recent order's shipping address only until
        // the user explicitly saves one of their own via the Address tab.
        $lastOrder = $orders->first();

        // Dashboard tab stats — derived from the collection already fetched
        // above, not separate queries.
        $totalOrders = $orders->count();
        $totalSpent = $orders->sum('total');
        $paidOrdersCount = $orders->where('payment_status', 'paid')->count();
        $pendingPaymentOrdersCount = $orders->where('payment_status', 'pending')->count();
        $recentOrders = $orders->take(3);

        return view('shop.my_account', compact(
            'user', 'orders', 'lastOrder',
            'totalOrders', 'totalSpent', 'paidOrdersCount', 'pendingPaymentOrdersCount', 'recentOrders',
        ));
    }

    public function updateDetails(Request $request, ResponseService $rs)
    {
        $user = Auth::guard('web')->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email:rfc,filter|max:255|unique:users,email,'.$user->id,
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator, 'accountDetails')->withInput();
        }

        $fieldLabels = ['name' => 'Name', 'email' => 'Email address', 'phone_number' => 'Phone number'];
        $before = $user->only(array_keys($fieldLabels));

        $user->update($validator->validated());

        $changes = [];
        foreach ($fieldLabels as $field => $label) {
            if ($before[$field] !== $user->{$field}) {
                $changes[] = ['label' => $label, 'old' => $before[$field], 'new' => $user->{$field}];
            }
        }

        // Notify the *original* email — if $changes includes email itself, this
        // still reaches the real owner rather than only the newly-set address.
        if (!empty($changes)) {
            $user->sendAccountDetailsChangedNotification($before['email'], $changes);
        }

        return $request->ajax()
            ? $rs->setSuccessResponse('Account details updated successfully.', ['name' => $user->name])
            : back()->with('success', 'Account details updated successfully.');
    }

    public function updatePassword(Request $request, ResponseService $rs)
    {
        $user = Auth::guard('web')->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator, 'changePassword')->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            $message = 'Your current password is incorrect.';

            return $request->ajax()
                ? $rs->setErrorResponse($message)
                : back()->withErrors(['current_password' => $message], 'changePassword')->withInput();
        }

        $user->forceFill(['password' => Hash::make($request->password)])->save();
        $user->sendPasswordChangedNotification();

        return $request->ajax()
            ? $rs->setSuccessResponse('Password changed successfully.', [])
            : back()->with('success', 'Password changed successfully.');
    }

    /**
     * Saves the account's own address (used to prefill checkout — see
     * CheckoutController::index()) — separate from any past order's address,
     * which never changes retroactively.
     */
    public function updateAddress(Request $request, ResponseService $rs)
    {
        $user = Auth::guard('web')->user();

        $validator = Validator::make($request->all(), [
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\- ]{3,20}$/'],
            'country' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator, 'address')->withInput();
        }

        $user->update($validator->validated());

        return $request->ajax()
            ? $rs->setSuccessResponse('Address saved successfully.', [])
            : back()->with('success', 'Address saved successfully.');
    }
}
