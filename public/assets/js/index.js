$(document).ready(function () {
    // Select dirubah
    $(".selects").on("change", function () {
        let myValue = $(this).val();
        let myRoute = $(this).find(':selected').data('routes');

        window.location.href = myRoute;
    });


    // Ajax untuk menghilangkan angka notifikasi
    $("#bell-trigger").on("click", function (e) {
        e.preventDefault();
        let route = $(this).data('route');

        fetch(route, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).then(res => res.json()).then(res => {
            if (res) {
                $(".bell-notification").hide('fast');
                return false;
            }

            // if (res == null) {
            //     window.location.href = routeBack;
            // } else {
            //     $(this).parents().siblings('.loc1').find('.stok').text(res.qty);
            //     $(this).parents().siblings('.loc1').find('.total').text(new Intl.NumberFormat('id-ID', {
            //         style: 'currency',
            //         currency: 'IDR'
            //     }).format(res.qty * res.harga));
            //     // $(this).parents().siblings(".desc-1").find(".qty-info").text(res.qty);
            //     // $(this).parents().siblings(".desc-1").find(".qty-total").text(new Intl.NumberFormat('id-ID', {
            //     //     style: 'currency',
            //     //     currency: 'IDR'
            //     // }).format(res.qty * res.price));
            // }
        });
    });
});