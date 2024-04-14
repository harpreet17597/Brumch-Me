// var script = document.createElement('script');
// script.src = window.location.origin+"/js/common/ajax.js";
// script.type='module';
// document.head.appendChild(script);
$(document).ready(function() {
    
    let user_id = null;

    /*DataTable*/
    $('#customer_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls.dataTable,
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'index',
                orderable: false
            },
            {
                data: 'profile_image',
                name: 'profile_image',
                orderable: false

            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'phone_country',
                name: 'phone_country',
                orderable: false
            },
            {
                data: 'phone',
                name: 'phone',
                orderable: false
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'dob',
                name: 'dob',
                orderable: false
            },
            {
                data: 'street_address',
                name: 'street_address',
                orderable: false
            },
            {
                data: 'account_status',
                name: 'account_status',
                orderable: false
            },

        ]
    });

    $(document).on('click', '.swal_profile_verify_status_change', function(e) {
        e.preventDefault();
        user_id = $(this).data('user-id');
        Swal.fire({
            title: 'Are you sure to change status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (user_id) {
                    let action_url = urls.changeProfileVerificationStatus;
                    action_url = action_url.replace('id', user_id);
                    dynamicAjax(action_url, 'POST', {}).then((successRes) => {
                        $('#customer_table').DataTable().ajax.reload();
                    })
                    .catch((errorRes) => {
                       console.log(errorRes);
                    })
                }
            }
        });
    });

    $(document).on('click', '.swal_account_status_change', function(e) {
        e.preventDefault();
        user_id = $(this).data('user-id');
        Swal.fire({
            title: 'Are you sure to change status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (user_id) {
                    let action_url = urls.changeAccountStatus;
                    action_url = action_url.replace('id', user_id);
                    dynamicAjax(action_url, 'POST', {}).then((successRes) => {
                        $('#customer_table').DataTable().ajax.reload();
                    })
                    .catch((errorRes) => {
                       console.log(errorRes);
                    })
                }
            }
        });
    });

  
});