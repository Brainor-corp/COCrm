$(document).ready(function () {
    $(".ajax-multiselect-equipment").select2({
        multiple: true,
        tokenSeparators: [',', ' '],
        minimumInputLength: 2,
        debug: true,
        ajax: {
            url: '/searchEquipment',
            dataType: "json",
            type: "GET",
            data: function (params) {

                var queryParameters = {
                    term: params.term
                }
                return queryParameters;
            },
            processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            text: item.name,
                            value: item.id,
                            id: item.id,
                        }
                    })
                };
            }
        }
    });
});