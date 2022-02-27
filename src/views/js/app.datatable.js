hereIam.onLoad(
    class {
        static initDataTables() {
            jQuery.extend(jQuery.fn.dataTable.ext.classes, {
                    sWrapper: "dataTables_wrapper dt-bootstrap5",
                    sFilterInput: "form-control",
                    sLengthSelect: "form-select"
                });
                // Override a few defaults
                jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                    language: {
                        lengthMenu: "_MENU_",
                        search: "_INPUT_",
                        searchPlaceholder: "Search..",
                        info: "Page <strong>_PAGE_</strong> of <strong>_PAGES_</strong>",
                        paginate: {
                            first: '<i class="fa fa-angle-double-left"></i>',
                            previous: '<i class="fa fa-angle-left"></i>',
                            next: '<i class="fa fa-angle-right"></i>',
                            last: '<i class="fa fa-angle-double-right"></i>'
                        }
                    }
                });
                // Init full extra DataTable
                jQuery(".js-dataTable-full-pagination").DataTable({
                    pagingType: "full_numbers",
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 20, 50, 100],
                        [5, 10, 20, 50, 100]
                    ],
                    autoWidth: false
                });
        }
        static init() {
            this.initDataTables();
        }
    }.init()
);