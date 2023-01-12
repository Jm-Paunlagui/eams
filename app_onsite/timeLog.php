<?php
include '../config/config.php';
require GLOBAL_FUNC;
require CL_SESSION_PATH;
require CONNECT_PATH;
require ISLOGIN;

$page_title = "Attendance Log";

if (!($g_user_role == "ONSITE")) {
    header("Location: " . BASE_URL); //balik sa login then sa login aalamain kung anung role at saang page landing dapat
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <?php
    include DOMAIN_PATH . "/global/meta_data.php";
    include DOMAIN_PATH . "/global/include_top.php";
    ?>
</head>

<body class="d-flex flex-column h-100" style="font-family: Montserrat, sans-serif;">
    <!-- HEADER -->
    <?php include_once DOMAIN_PATH . "/global/top_bar.php"; ?>

    <main class="flex-shrink-0">
        <section>
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="page-title mt-4">
                                    <i class="mdi mdi-apple-keyboard-command title_icon">
                                    </i> <?php echo $page_title; ?>
                                </h4>
                                <div class="d-md-inline-flex justify-content-between align-items-center mb-3 mt-3 border-bottom pb-3 d-lg-flex">

                                    <div class="w-100 p-md-2">
                                        <label for="filter-field" class="form-label">
                                            Filter BY:
                                        </label>
                                        <select id="filter-field" class="form-select">
                                            <option value="date_log">
                                                Date
                                            </option>
                                            <option value="employee_id">
                                                Employee ID
                                            </option>
                                            <option value="time_in">
                                                Time In
                                            </option>
                                            <option value="break_out">
                                                Break Out
                                            </option>
                                            <option value="break_in">
                                                Break In
                                            </option>
                                            <option value="time_out">
                                                Time Out
                                            </option>
                                            <option value="way">
                                                Status
                                            </option>
                                        </select>
                                    </div>
                                    <div class="w-100 p-lg-2">
                                        <label for="filter-value" class="form-label">
                                            Filter Value:
                                        </label>
                                        <input type="text" id="filter-value" class="form-control">
                                    </div>
                                </div>
                                <div id="example-table" class="table table-borderless"></div>
                                <form method="post">
                                    <button id="print-table" type="button" class="btn btn-primary">Print</button>
                                </form>

                            </div>
                        </div> <!-- end col-->
                    </div>

                </div>
            </div>
        </section>
    </main>
    <?php
        include DOMAIN_PATH."/app_onsite/settings.php";
        include FOOTER_PATH;
    ?>
</body>

</html>
<?php
// script, function
include DOMAIN_PATH . "/global/include_bottom.php";
?>

<script>
    // employees attedance
    (function() {
        // Define variables for input elements
        const fieldEl = document.getElementById("filter-field");
        const typeEl = document.getElementById("filter-type");
        const valueEl = document.getElementById("filter-value");

        function handleFilterChange() {
            // Get the value of the filter input elements
            const field = fieldEl.value;
            const type = "like";
            const value = valueEl.value;

            // Clear any existing filters on the table
            table.clearFilter();

            // If all of the values are empty, don't filter the table
            if (!field || !type || !value) {
                return;
            }

            // Set the filter for the chosen column
            table.setFilter(field, type, value);
        }

        // Add event listeners to the filter input elements
        fieldEl.addEventListener("change", handleFilterChange);
        valueEl.addEventListener("change", handleFilterChange);
        var table = new Tabulator("#example-table", {

            ajaxSorting: true,
            ajaxFiltering: true,
            height: "500px",
            tooltips: true,
            printAsHtml: true,
            headerFilterPlaceholder: "",
            layout: "fitColumns",
            placeholder: "No Data Found",
            movableColumns: true,
            selectable: true,
            ajaxURL: "<?php echo BASE_URL; ?>app_onsite/timeLog_db.php",
            ajaxProgressiveLoad: "scroll",
            ajaxProgressiveLoadScrollMargin: 1,
            printConfig: {
                columnGroups: false,
                rowGroups: false,
            },
            ajaxLoader: true,
            ajaxLoaderLoading: 'Fetching data from Database..',
            selectableRollingSelection: false,
            paginationSize: <?php echo QUERY_LIMIT; ?>,

            columns: [
                // logs employee_id, card_id, time_in, time_out, break_in, break_out, date_log
                {
                    title: "Date",
                    field: "date_log",
                    formatter: "datetime",
                    formatterParams: {
                        inputFormat: "YYYY-MM-DD",
                        outputFormat: "MM-DD-YYYY",
                        invalidPlaceholder: "--",
                    },
                    align: "center",
                },
                {
                    title: "Employee ID",
                    field: "employee_id",
                    align: "center"
                },
                // Format datetime to time only
                {
                    title: "Time In",
                    field: "time_in",
                    formatter: "datetime",
                    formatterParams: {
                        inputFormat: "YYYY-MM-DD hh:mm:ss",
                        outputFormat: "h:mm:ss A",
                        invalidPlaceholder: "--",
                    },
                    align: "center"
                },
                {
                    title: "Break Out",
                    field: "break_out",
                    formatter: "datetime",
                    formatterParams: {
                        inputFormat: "YYYY-MM-DD hh:mm:ss",
                        outputFormat: "h:mm:ss A",
                        invalidPlaceholder: "--",
                    },
                    align: "center"
                },
                {
                    title: "Break In",
                    field: "break_in",
                    formatter: "datetime",
                    formatterParams: {
                        inputFormat: "YYYY-MM-DD hh:mm:ss",
                        outputFormat: "h:mm:ss A",
                        invalidPlaceholder: "--",
                    },
                    align: "center"
                },
                {
                    title: "Time Out",
                    field: "time_out",
                    formatter: "datetime",
                    formatterParams: {
                        inputFormat: "YYYY-MM-DD hh:mm:ss",
                        outputFormat: "h:mm:ss A",
                        invalidPlaceholder: "--",
                    },
                    align: "center"
                },


            ],
            ajaxResponse: function(url, params, response) {
                //url - the URL of the request
                //params - the parameters passed with the request
                //response - the JSON object returned in the body of the response.
                return response; //return the tableData property of a response json object
            },

        });
    })();
</script>