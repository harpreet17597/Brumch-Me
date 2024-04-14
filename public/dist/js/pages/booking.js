// var script = document.createElement('script');
// script.src = window.location.origin+"/js/common/ajax.js";
// script.type='module';
// document.head.appendChild(script);
$(document).ready(function() {
    
    let user_id = null;

    /*DataTable*/
    $('#booking_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls.dataTable,
        },
        columns: [
            {
                data: 'booking_number',
                name: 'booking number',
                orderable: false
            },
            {
                data: 'booking_from_date_time',
                name: 'booking from date time',
                orderable: false
            },
            {
                data: 'booking_to_date_time',
                name: 'booking to date time',
                orderable: false
            },
            {
                data: 'customer_details.name',
                name: 'customer name',
                orderable: false
            },
            {
                data: 'customer_details.phone',
                name: 'customer phone',
                orderable: false
            },
            {
                data: 'restaurant_details.restaurant_name',
                name: 'booking_number',
                orderable: false
            },
            {
                data: 'status',
                name: 'booking status',
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
                        $('#business_table').DataTable().ajax.reload();
                    })
                    .catch((errorRes) => {
                       console.log(errorRes);
                    })
                }
            }
        });
    });

  
});