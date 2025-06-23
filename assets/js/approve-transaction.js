$(document).ready(function () {
    // Initialize basic DataTable
    $("#basic-datatables").DataTable({});

    // Initialize DataTable with multi-filter select
    $("#multi-filter-select").DataTable({
        pageLength: 5,
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var select = $('<select class="form-select"><option value=""></option></select>')
                    .appendTo($(column.footer()).empty())
                    .on("change", function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? "^" + val + "$" : "", true, false).draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + "</option>");
                });
            });
        }
    });

    // Initialize DataTable for add-row functionality
    $("#add-row").DataTable({
        pageLength: 5
    });

    // Action buttons template
    var action = '<td> <div class="form-button-action">' +
        '<button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task">' +
        '<i class="fa fa-edit"></i></button>' +
        '<button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove">' +
        '<i class="fa fa-times"></i></button>' +
        '</div></td>';

    // Add row button click handler
    $("#addRowButton").click(function () {
        $("#add-row").DataTable().fnAddData([
            $("#addName").val(),
            $("#addPosition").val(),
            $("#addOffice").val(),
            action
        ]);
        $("#addRowModal").modal("hide");
    });
});