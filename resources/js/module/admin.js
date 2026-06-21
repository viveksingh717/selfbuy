import '../../css/module/admin.css';
// import Dropzone from "dropzone";
import { formatPrice, showError, clearError, autoGenerateSlug, showGlobalError } from '../utils';
Dropzone.autoDiscover = false;

// $(function () {

//     init();

// });

// function init()
// {
//     categoryModule();
//     productModule();
// }

// function categoryModule()
// {
//     if ($('#categoryForm').length) {

//         $('#categoryForm').on('submit', function (e) {

//         e.preventDefault();

//         // add category ajax

//     });

//     }

//     $(document).on('click', '.delete-category', function () {

//         // delete category

//     });
// }

$(function () {
    /*
    |--------------------------------------------------------------------------
    | Category Module
    |--------------------------------------------------------------------------
    */
    $(document).on('input change', '.form-control, select, textarea', function () {
        if (this.id) {
            clearError(this.id);
        }
    });
    autoGenerateSlug('#category_name', '#category_slug');
    autoGenerateSlug('#subcategory_name', '#subcategory_slug');
    autoGenerateSlug('#brand_name', '#brand_slug');
    autoGenerateSlug('#product_name', '#product_slug');

    if ($('#categoryForm').length || $('#editCategoryForm').length)  {

        // Dropzone.autoDiscover = false;

        if ($("#categoryDropzone").length && !$("#categoryDropzone")[0].dropzone) {

            let categoryDropzone = new Dropzone("#categoryDropzone", {

                url: "/admin/category_image_upload",
                method: "POST",
                paramName: "category_image",

                maxFiles: 1,
                maxFilesize: 2,

                acceptedFiles: ".jpeg,.jpg,.png,.gif,.svg",

                addRemoveLinks: true,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function (file, response) {

                    if (response.success) {
                        $('#category_image').val(response.file_name);

                    } else {

                        showGlobalError(response.message);

                        this.removeFile(file);
                    }
                },

                error: function (file, response) {

                    let message = 'Image upload failed';

                    if (typeof response === 'object' && response.message) {
                        message = response.message;
                    }

                    showGlobalError(message);

                    this.removeFile(file);
                },

                removedfile: function (file) {

                    $('#category_image').val('');

                    if (file.previewElement) {
                        file.previewElement.remove();
                    }
                }
            });
        }

        $('#categoryForm, #editCategoryForm').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let button = $(this).find('button[type="submit"]');
            let url = form.attr('id') === 'editCategoryForm'
                ? '/admin/update_category/' + form.find('[name="id"]').val()
                : '/admin/process_category';
            button.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editCategoryForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    if (response.success == true) {
                        window.location.href = '/admin/category';
                        $('#categoryForm')[0].reset();
                        $('#editCategoryForm')[0].reset();
                    } else {
                        showGlobalError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editCategoryForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    console.log(xhr.responseText);

                    let res = xhr.responseJSON;
                    let firstField = null;

                    if (xhr.status === 422 && res.validation) {

                        // loop through validation errors
                         $.each(res.validation, function (field, message) {

                            showError(field, message, false, false);

                            if (!firstField) {
                                firstField = field;
                            }
                        });

                        if (firstField) {
                            showError(firstField, res.validation[firstField], true, true);
                        }
                    } else if (res && res.message) {
                        showGlobalError('An unexpected error occurred. Please try again later.');
                    } else {
                        showGlobalError('Something went wrong. Please try again later.');
                    }
                }
            });

        });

    }

    $('#categoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/category',

        columns: [
            {
                data: 'id',
                name: 'id'
            },

            {
                data: 'category_name',
                name: 'category_name'
            },

            {
                data: 'category_slug',
                name: 'category_slug'
            },

            {
                data: 'category_image',
                name: 'category_image',
            },

            {
                data: 'status',
                name: 'status'
            },

            {
                data: 'is_featured',
                name: 'is_featured'
            },

            {
                data: 'created_at',
                name: 'created_at'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],

        order: [[0, 'desc']]
    });

    $(document).on('click', '.delete-category', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Category will be deleted permanently!",
            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '/admin/delete_category/' + id,
                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#categoryTable').DataTable().ajax.reload(null, false);

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },

                    error: function (xhr) {

                        let res = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res?.message || 'Something went wrong'
                        });
                    }

                });

            }

        });

    });

    //Sub Category Module
    if ($('#subCategoryForm').length || $('#editSubCategoryForm').length)  {

        $('#subCategoryForm, #editSubCategoryForm').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let button = $(this).find('button[type="submit"]');
            let url = form.attr('id') === 'editSubCategoryForm'
                ? '/admin/update_subcategory/' + form.find('[name="id"]').val()
                : '/admin/process_subcategory';
            button.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editSubCategoryForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    if (response.success == true) {
                        window.location.href = '/admin/sub_category';
                        $('#subCategoryForm')[0].reset();
                        $('#editSubCategoryForm')[0].reset();
                    } else {
                        showGlobalError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editSubCategoryForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    console.log(xhr.responseText);

                    let res = xhr.responseJSON;
                    let firstField = null;

                    if (xhr.status === 422 && res.validation) {

                        // loop through validation errors
                         $.each(res.validation, function (field, message) {

                            showError(field, message, false, false);

                            if (!firstField) {
                                firstField = field;
                            }
                        });

                        if (firstField) {
                            showError(firstField, res.validation[firstField], true, true);
                        }
                    } else if (res && res.message) {
                        showGlobalError('An unexpected error occurred. Please try again later.');
                    } else {
                        showGlobalError('Something went wrong. Please try again later.');
                    }
                }
            });

        });

    }

    $('#subCategoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/sub_category',

        columns: [
            {
                data: 'id',
                name: 'id'
            },

            {
                data: 'subcategory_name',
                name: 'subcategory_name'
            },

            {
                data: 'subcategory_slug',
                name: 'subcategory_slug'
            },

            {
                data: 'category',
                name: 'category'
            },

            {
                data: 'status',
                name: 'status'
            },

            {
                data: 'created_at',
                name: 'created_at'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],

        order: [[0, 'desc']]
    });

    $(document).on('click', '.delete-subcategory', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Category will be deleted permanently!",
            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '/admin/delete_subcategory/' + id,
                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#subCategoryTable').DataTable().ajax.reload(null, false);

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },

                    error: function (xhr) {

                        let res = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res?.message || 'Something went wrong'
                        });
                    }

                });

            }

        });

    });

    // Brand Module
    if ($('#brandForm').length || $('#editBrandForm').length)  {

        // Dropzone.autoDiscover = false;

        if ($("#brandDropzone").length && !$("#brandDropzone")[0].dropzone) {

            let brandDropzone = new Dropzone("#brandDropzone", {

                url: "/admin/brand_image_upload",
                method: "POST",
                paramName: "brand_image",

                maxFiles: 1,
                maxFilesize: 2,

                acceptedFiles: ".jpeg,.jpg,.png,.gif,.svg",

                addRemoveLinks: true,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function (file, response) {

                    if (response.success) {
                        $('#brand_image').val(response.file_name);

                    } else {
                        showGlobalError(response.message);

                        this.removeFile(file);
                    }
                },

                error: function (file, response) {

                    let message = 'Image upload failed';

                    if (typeof response === 'object' && response.message) {
                        message = response.message;
                    }

                    showGlobalError(message);

                    this.removeFile(file);
                },

                removedfile: function (file) {

                    $('#brand_image').val('');

                    if (file.previewElement) {
                        file.previewElement.remove();
                    }
                }
            });
        }

        $('#brandForm, #editBrandForm').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let button = $(this).find('button[type="submit"]');
            let url = form.attr('id') === 'editBrandForm'
                ? '/admin/update_brand/' + form.find('[name="id"]').val()
                : '/admin/process_brand';
            button.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editBrandForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    if (response.success == true) {
                        window.location.href = '/admin/brand';
                        $('#brandForm')[0].reset();
                        $('#editBrandForm')[0].reset();
                    } else {
                        showGlobalError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editBrandForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    console.log(xhr.responseText);

                    let res = xhr.responseJSON;
                    let firstField = null;

                    if (xhr.status === 422 && res.validation) {

                        // loop through validation errors
                         $.each(res.validation, function (field, message) {

                            showError(field, message, false, false);

                            if (!firstField) {
                                firstField = field;
                            }
                        });

                        if (firstField) {
                            showError(firstField, res.validation[firstField], true, true);
                        }
                    } else if (res && res.message) {
                        showGlobalError('An unexpected error occurred. Please try again later.');
                    } else {
                        showGlobalError('Something went wrong. Please try again later.');
                    }
                }
            });

        });

    }

    $('#brandTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/brand',

        columns: [
            {
                data: 'id',
                name: 'id'
            },

            {
                data: 'brand_name',
                name: 'brand_name'
            },

            {
                data: 'brand_slug',
                name: 'brand_slug'
            },

            {
                data: 'brand_image',
                name: 'brand_image',
            },

            {
                data: 'status',
                name: 'status'
            },

            {
                data: 'created_at',
                name: 'created_at'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],

        order: [[0, 'desc']]
    });

    $(document).on('click', '.delete-brand', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Brand will be deleted permanently!",
            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '/admin/delete_brand/' + id,
                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#brandTable').DataTable().ajax.reload(null, false);

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },

                    error: function (xhr) {

                        let res = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res?.message || 'Something went wrong'
                        });
                    }

                });

            }

        });

    });

    // Color Module
    if ($('#colorForm').length || $('#editColorForm').length) {
        $('#colorForm, #editColorForm').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let button = $(this).find('button[type="submit"]');
            let url = form.attr('id') === 'editColorForm'
                ? '/admin/update_color/' + form.find('[name="id"]').val()
                : '/admin/process_color';
            button.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editColorForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    if (response.success == true) {
                        window.location.href = '/admin/color';
                        $('#colorForm')[0].reset();
                        $('#editColorForm')[0].reset();
                    } else {
                        showGlobalError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editColorForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    console.log(xhr.responseText);

                    let res = xhr.responseJSON;
                    let firstField = null;

                    if (xhr.status === 422 && res.validation) {

                        // loop through validation errors
                         $.each(res.validation, function (field, message) {

                            showError(field, message, false, false);

                            if (!firstField) {
                                firstField = field;
                            }
                        });

                        if (firstField) {
                            showError(firstField, res.validation[firstField], true, true);
                        }
                    } else if (res && res.message) {
                        showGlobalError('An unexpected error occurred. Please try again later.');
                    } else {
                        showGlobalError('Something went wrong. Please try again later.');
                    }
                }
            });

        });
    }

    $('#colorTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/color',

        columns: [
            {
                data: 'id',
                name: 'id'
            },

            {
                data: 'color_name',
                name: 'color_name'
            },

            {
                data: 'color_code',
                name: 'color_code'
            },

            {
                data: 'status',
                name: 'status'
            },

            {
                data: 'created_at',
                name: 'created_at'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],

        order: [[0, 'desc']]
    });

    $(document).on('click', '.delete-color', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Color will be deleted permanently!",
            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '/admin/delete_color/' + id,
                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#colorTable').DataTable().ajax.reload(null, false);

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },

                    error: function (xhr) {

                        let res = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res?.message || 'Something went wrong'
                        });
                    }

                });

            }

        });
    });

    // Size Module
    if ($('#sizeForm').length || $('#editSizeForm').length) {
        $('#sizeForm, #editSizeForm').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let button = $(this).find('button[type="submit"]');
            let url = form.attr('id') === 'editSizeForm'
                ? '/admin/update_size/' + form.find('[name="id"]').val()
                : '/admin/process_size';
            button.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editSizeForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    if (response.success == true) {
                        window.location.href = '/admin/size';
                        $('#sizeForm')[0].reset();
                        $('#editSizeForm')[0].reset();
                    } else {
                        showGlobalError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editSizeForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    console.log(xhr.responseText);

                    let res = xhr.responseJSON;
                    let firstField = null;

                    if (xhr.status === 422 && res.validation) {

                        // loop through validation errors
                         $.each(res.validation, function (field, message) {

                            showError(field, message, false, false);

                            if (!firstField) {
                                firstField = field;
                            }
                        });

                        if (firstField) {
                            showError(firstField, res.validation[firstField], true, true);
                        }
                    } else if (res && res.message) {
                        showGlobalError('An unexpected error occurred. Please try again later.');
                    } else {
                        showGlobalError('Something went wrong. Please try again later.');
                    }
                }
            });

        });
    }

    $('#sizeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/size',

        columns: [
            {
                data: 'id',
                name: 'id'
            },

            {
                data: 'size_name',
                name: 'size_name'
            },

            {
                data: 'size_code',
                name: 'size_code'
            },

            {
                data: 'status',
                name: 'status'
            },

            {
                data: 'created_at',
                name: 'created_at'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],

        order: [[0, 'desc']]
    });

     $(document).on('click', '.delete-size', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Size will be deleted permanently!",
            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '/admin/delete_size/' + id,
                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#sizeTable').DataTable().ajax.reload(null, false);

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },

                    error: function (xhr) {

                        let res = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res?.message || 'Something went wrong'
                        });
                    }

                });

            }

        });

    });

    // Coupon Module
    if ($('#couponForm').length || $('#editCouponForm').length) {
        $('#couponForm, #editCouponForm').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let button = $(this).find('button[type="submit"]');
            let url = form.attr('id') === 'editCouponForm'
                ? '/admin/update_coupon/' + form.find('[name="id"]').val()
                : '/admin/process_coupon';
            button.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editCouponForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    if (response.success == true) {
                        window.location.href = '/admin/coupon';
                        $('#couponForm')[0].reset();
                        $('#editCouponForm')[0].reset();
                    } else {
                        showGlobalError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editCouponForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    console.log(xhr.responseText);

                    let res = xhr.responseJSON;
                    let firstField = null;

                    if (xhr.status === 422 && res.validation) {

                        // loop through validation errors
                         $.each(res.validation, function (field, message) {

                            showError(field, message, false, false);

                            if (!firstField) {
                                firstField = field;
                            }
                        });

                        if (firstField) {
                            showError(firstField, res.validation[firstField], true, true);
                        }
                    } else if (res && res.message) {
                        showGlobalError('An unexpected error occurred. Please try again later.');
                    } else {
                        showGlobalError('Something went wrong. Please try again later.');
                    }
                }
            });
        });
    }

    $('#couponTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/coupon',

        columns: [
            { data: 'id',            name: 'id' },
            { data: 'coupon_name',   name: 'coupon_name' },
            { data: 'coupon_code',   name: 'coupon_code' },
            { data: 'discount_type', name: 'discount_type' },
            { data: 'discount_value', name: 'discount_value' },
            { data: 'expiry_date',   name: 'expiry_date' },
            { data: 'status',        name: 'status', orderable: false, searchable: false },
            { data: 'created_at',    name: 'created_at' },
            { data: 'action',        name: 'action', orderable: false, searchable: false }
        ],

        order: [[0, 'desc']]
    });

    $(document).on('click', '.delete-coupon', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Coupon will be deleted permanently!",
            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '/admin/delete_coupon/' + id,
                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#couponTable').DataTable().ajax.reload(null, false);

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },

                    error: function (xhr) {

                        let res = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res?.message || 'Something went wrong'
                        });
                    }

                });

            }

        });
    });

    // $(document).on('change', '.toggle-status', function () {

    //     const id = $(this).data('id');
    //     const status = $(this).is(':checked') ? 1 : 0;

    //     $.ajax({
    //         url: '/admin/coupon_status/' + id,
    //         type: 'POST',
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: {
    //             status: status
    //         },
    //         success: function (res) {
    //             toastr.success(res.message);
    //             $('#couponTable').DataTable().ajax.reload(null, false);
    //         },
    //         error: function () {
    //             toastr.error('Failed to update status.');
    //         }
    //     });
    // });

    $(document).on('change', '.toggle-status', function () {

        const $this = $(this);
        const id = $this.data('id');
        const status = $this.is(':checked') ? 1 : 0;
        const url = $this.data('url');
        const table = $this.data('table');

        $.ajax({
            url: url + '/' + id,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                status: status
            },
            success: function (res) {

                if (typeof toastr !== 'undefined') {
                    toastr.success(res.message);
                }

                if (table) {
                    $(table).DataTable().ajax.reload(null, false);
                }
            },
            error: function () {

                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update status.');
                }
            }
        });
    });

    // Tax Module
    if ($('#taxForm').length || $('#editTaxForm').length) {
        $('#taxForm, #editTaxForm').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let button = $(this).find('button[type="submit"]');
            let url = form.attr('id') === 'editTaxForm'
                ? '/admin/update_tax/' + form.find('[name="id"]').val()
                : '/admin/process_tax';
            button.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editTaxForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    if (response.success == true) {
                        window.location.href = '/admin/tax';
                        $('#taxForm')[0].reset();
                        $('#editTaxForm')[0].reset();
                    } else {
                        showGlobalError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editTaxForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    console.log(xhr.responseText);

                    let res = xhr.responseJSON;
                    let firstField = null;

                    if (xhr.status === 422 && res.validation) {

                        // loop through validation errors
                         $.each(res.validation, function (field, message) {

                            showError(field, message, false, false);

                            if (!firstField) {
                                firstField = field;
                            }
                        });

                        if (firstField) {
                            showError(firstField, res.validation[firstField], true, true);
                        }
                    } else if (res && res.message) {
                        showGlobalError('An unexpected error occurred. Please try again later.');
                    } else {
                        showGlobalError('Something went wrong. Please try again later.');
                    }
                }
            });
        });
    }

    $('#taxTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/tax',

        columns: [
            { data: 'id', name: 'id' },
            { data: 'tax_name', name: 'tax_name' },
            { data: 'tax_type', name: 'tax_type' },
            { data: 'tax_rate', name: 'tax_rate' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],

        order: [[0, 'desc']]
    });

    $(document).on('click', '.delete-tax', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Tax will be deleted permanently!",
            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '/admin/delete_tax/' + id,
                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#taxTable').DataTable().ajax.reload(null, false);

                        } else {

                            Swal.fire   ({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },

                    error: function (xhr) {

                        let res = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res?.message || 'Something went wrong'
                        });
                    }

                });

            }

        });

    });

    //Product Module
    if ($('#productForm').length || $('#editProductForm').length) {
        // Product form submission logic will go here

        // ── Product Thumbnail Dropzone ────────────────────────────────
        if ($('#thumbnailDropzone').length && !$('#thumbnailDropzone')[0].dropzone) {

            let thumbnailDropzone = new Dropzone('#thumbnailDropzone', {

                url: '/admin/product_image_upload',
                method: 'POST',
                paramName: 'product_image',

                maxFiles: 1,
                maxFilesize: 2,
                acceptedFiles: '.jpeg,.jpg,.png,.gif,.svg',
                addRemoveLinks: true,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function (file, response) {
                    if (response.success) {
                        $('#product_image').val(response.file_name);
                    } else {
                        showGlobalError(response.message);
                        this.removeFile(file);
                    }
                },

                error: function (file, response) {
                    let message = 'Thumbnail upload failed';
                    if (typeof response === 'object' && response.message) {
                        message = response.message;
                    }
                    showGlobalError(message);
                    this.removeFile(file);
                },

                removedfile: function (file) {
                    $('#product_image').val('');
                    if (file.previewElement) {
                        file.previewElement.remove();
                    }
                }
            });
        }

        // ── Gallery Dropzone ──────────────────────────────────
        let galleryFileNames = [];

        if ($('#galleryDropzone').length && !$('#galleryDropzone')[0].dropzone) {

            let galleryDropzone = new Dropzone('#galleryDropzone', {

                url: '/admin/product_gallery_upload',
                method: 'POST',
                paramName: 'gallery_image',

                maxFiles: 8,
                maxFilesize: 2,
                acceptedFiles: '.jpeg,.jpg,.png,.gif,.svg',
                addRemoveLinks: true,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function (file, response) {
                    if (response.success) {
                        // store filename in array and sync hidden inputs
                        galleryFileNames.push(response.file_name);
                        file.serverFileName = response.file_name;
                        syncGalleryInputs();
                    } else {
                        showGlobalError(response.message);
                        this.removeFile(file);
                    }
                },

                error: function (file, response) {
                    let message = 'Gallery image upload failed';
                    if (typeof response === 'object' && response.message) {
                        message = response.message;
                    }
                    showGlobalError(message);
                    this.removeFile(file);
                },

                removedfile: function (file) {
                    // remove this file from array
                    if (file.serverFileName) {
                        galleryFileNames = galleryFileNames.filter(
                            name => name !== file.serverFileName
                        );
                        syncGalleryInputs();
                    }
                    if (file.previewElement) {
                        file.previewElement.remove();
                    }
                }
            });
        }

        // sync gallery filenames into hidden inputs
        function syncGalleryInputs() {
            const container = document.getElementById('galleryImageNames');
            container.innerHTML = '';
            galleryFileNames.forEach(function (name) {
                const input   = document.createElement('input');
                input.type    = 'hidden';
                input.name    = 'gallery_images[]';
                input.value   = name;
                container.appendChild(input);
            });
        }

        $('#productForm, #editProductForm').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let button = $(this).find('button[type="submit"]');
            let url = form.attr('id') === 'editProductForm'
                ? '/admin/update_product/' + form.find('[name="id"]').val()
                : '/admin/process_product';
            button.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editProductForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    if (response.success == true) {
                        window.location.href = '/admin/product';
                        $('#productForm')[0].reset();
                        $('#editProductForm')[0].reset();
                    } else {
                        showGlobalError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    button.prop('disabled', false).text(
                        form.attr('id') === 'editProductForm'
                            ? 'Update'
                            : 'Submit'
                    );
                    console.log(xhr.responseText);

                    let res = xhr.responseJSON;
                    let firstField = null;

                    if (xhr.status === 422 && res.validation) {

                        // loop through validation errors
                         $.each(res.validation, function (field, message) {

                            showError(field, message, false, false);

                            if (!firstField) {
                                firstField = field;
                            }
                        });

                        if (firstField) {
                            showError(firstField, res.validation[firstField], true, true);
                        }
                    } else if (res && res.message) {
                        showGlobalError('An unexpected error occurred. Please try again later.');
                    } else {
                        showGlobalError('Something went wrong. Please try again later.');
                    }
                }
            });

        });
    }

    $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/admin/product',

        columns: [
            { data: 'id', name: 'id' },
            { data: 'product_name', name: 'product_name' },
            { data: 'sku', name: 'sku' },
            { data: 'product_image', name: 'product_image' },
            { data: 'category', name: 'category' },
            { data: 'brand', name: 'brand' },
            { data: 'price', name: 'price' },
            { data: 'cost_price', name: 'cost_price' },
            { data: 'discount', name: 'discount' },
            { data: 'stock_status', name: 'stock_status' },
            { data: 'flags', name: 'flags', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],

        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-2x"></i>',
        }
    });

    $(document).on('click', '.delete-product', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Product will be deleted permanently!",
            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '/admin/delete_product/' + id,
                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        if (response.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#productTable').DataTable().ajax.reload(null, false);

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },

                    error: function (xhr) {

                        let res = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res?.message || 'Something went wrong'
                        });
                    }

                });

            }

        });
    });


});
