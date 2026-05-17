<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>LC report</title>
    <link rel="stylesheet" href="./style.css"/>
    <script src="./script.js" defer></script>
    <style>
        button.join-item.btn.btn-active {
            background-color: color-mix(in oklab, oklch(var(--btn-color, var(--b2)) / var(--tw-bg-opacity, 1)) 90%, black);
            border-color: color-mix(in oklab, oklch(var(--btn-color, var(--b2)) / var(--tw-border-opacity, 1)) 90%, black);
        }
    </style>
</head>
<body>
<div class="navbar bg-base-100 shadow-sm">
    <div class="navbar-start">
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                >
                    <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h7"
                    />
                </svg>
            </div>

        </div>
    </div>
    <div class="navbar-center">
        <a class="btn btn-ghost text-xl">LC report</a>
    </div>
    <div class="navbar-end">
        <button class="btn btn-ghost btn-circle">
            <div class="indicator" onclick="toggleTheme()">
                <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5"
                >
                    <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                    />
                </svg>
            </div>
        </button>
    </div>
</div>
<div class="px-4 py-2 md:px-12 md:py-4">
    <?php

    // Database credentials
    $hostname = 'localhost';
    $username = 'root';
    $password = ''; // No password
    $database = 'resindb2022'; // Replace with your actual database name

    // Create a connection
    $connection = mysqli_connect($hostname, $username, $password, $database);

    // Check the connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $total_dr = 0;
    $total_cr = 0;

    $from_date = "";
    $to_date = "";
    $description = "";
    $from = 0;
    $to = 30;
    $page = 1;


    if (isset($_SERVER["REQUEST_METHOD"]) && isset($_REQUEST['submit']) && $_SERVER["REQUEST_METHOD"] == "POST") {
        $from_date = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "";
        $to_date = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "";
        $description = isset($_REQUEST['lc_number']) ? $_REQUEST['lc_number'] : "";

        if (isset($_REQUEST['from'])) {
            $from = is_numeric($_REQUEST['from']) ? (int)$_REQUEST['from'] : $from;
        }
        if (isset($_REQUEST['to'])) {
            $to = is_numeric($_REQUEST['to']) ? (int)$_REQUEST['to'] : $to;
        }
        if (isset($_REQUEST['page'])) {
            $page = is_numeric($_REQUEST['page']) ? (int)$_REQUEST['page'] : $page;
        }
    }

    // SQL query
    $sql = "SELECT * 
                            FROM sub_acc_head 
                            INNER JOIN account_journal ON sub_acc_head.sub_id = account_journal.sub_id 
                            WHERE account_journal.description LIKE 'lc%$description'";

    if ($from_date != "" && $to_date == "") {
        $sql .= " AND account_journal.created_date >= '$from_date'";
    } elseif ($from_date == "" && $to_date != "") {
        $sql .= " AND account_journal.created_date <= '$to_date'";
    } elseif ($from_date != "" && $to_date != "") {
        $sql .= " AND account_journal.created_date BETWEEN '$from_date' AND '$to_date'";
    }


    // Execute the query
    $totalResult = mysqli_query($connection, $sql);

    $sql .= " LIMIT $from,$to";
    $result = mysqli_query($connection, $sql);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . mysqli_error($connection));
    }


    ?>
    <h1 class="text-3xl font-bold mb-3"></h1>
    <form
            id="lcForm"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
            method="POST"
            class="px-1 py-2 md:px-7 md:py-8 shadow-lg rounded-xl flex items-end flex-wrap gap-3 ml-2"
    >
        <label class="form-control w-full max-w-xs md:max-w-sm">
            <div class="label">
                <span class="label-text font-bold">From</span>
            </div>
            <input
                    id="from_date"
                    name="from_date"
                    value="<?php echo isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : '' ?>"
                    type="date"
                    class="input input-ghost input-bordered hover:bg-base-200 w-full max-w-xs md:max-w-sm"
            />

            <input type="hidden" id="from" name="from"
                   value="0"/>
            <input type="hidden" id="to" name="to"
                   value=""/>
            <input type="hidden" id="page" name="page"
                   value=""/>
        </label>
        <label class="form-control w-full max-w-xs md:max-w-sm">
            <div class="label">
                <span class="label-text font-bold">To</span>
            </div>
            <input
                    id="to_date"
                    name="to_date"
                    type="date"
                    value="<?php echo isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '' ?>"
                    class="input input-ghost input-bordered hover:bg-base-200 w-full max-w-xs md:max-w-sm"
            />
        </label>
        <label class="form-control flex-1 relative">
            <div class="label">
                <span class="label-text font-bold">LC Number</span>
            </div>
            <input
                    id="lc_number"
                    name="lc_number"
                    value="<?php echo isset($description) ? $description : '' ?>"
                    type="text"
                    placeholder="Type here"
                    class="input input-ghost input-bordered hover:bg-base-200 w-full"
            />
            <!--            <div class="input-suggestions-dd active">-->
            <!--                 add all the batch list -->
            <!--                <div class="input-suggestion">have to find</div>-->
            <!--                <div class="input-suggestion">taddakdd</div>-->
            <!--                <div class="input-suggestion">hello, world</div>-->
            <!--            </div>-->
        </label>
        <button name="submit" type="submit" class="btn btn-primary rounded-lg">Submit
        </button>
    </form>
    <div class="py-3 md:py-8 px-2 md:px-5 mt-3 rounded-lg shadow-lg ml-2">
        <div
                class="flex items-center justify-center sm:justify-between gap-3 flex-wrap mb-3"
        >
            <h2 class="text-xl font-bold mb-3"></h2>
            <div class="join">
                <?php

                $totalrecord = $totalResult->num_rows;

                $block = $to;

                $callFunc = "nextPage";

                $from_rs = $page;
                if ($from_rs == "") {
                    $from_rs = 0;
                }
                if ($block == "") {
                    $block = 12;
                }
                $to_rs = (int)$from_rs + $block;
                if ($from_rs >= $block) {
                    $from_rs = $from_rs + 1;
                }
                if ($from_rs == "" || $from_rs == 0) {
                    $from_rs = 1;
                }
                if ($to_rs == "" || $totalrecord < $block) {
                    $to_rs = $totalrecord;
                } else if ($to_rs == "" && $totalrecord > $block) {
                    $to_rs = $block;
                }
                if ($to_rs > $totalrecord) {
                    $to_rs = $totalrecord;
                }
                if ($totalrecord == 0) {
                    $from_rs = 0;
                }

                $plink = $page;
                if ($plink == "") {
                    $plink = 1;
                }
                if ($totalrecord > $block) {
                    $res = $totalrecord / $block;
                    $res = (int)$res;
                    if (($totalrecord % $block) != 0) {
                        $totalpage = $res + 1;
                    } else {
                        $totalpage = $res;
                    }
                } else {
                    $totalpage = 1;
                }
                $paginationStr = "";

                if ($totalrecord > $block) {
                    $two = $from;
                    if ($two == "") {
                        $two = 0;
                    }
                    $pno = $page;
                    if ($pno == "") {
                        $pno = 0;
                    }
                    $pno = $pno - 1;
                    $frm = $two - $block;
                    $to = $block;
                    if ($pno <= $totalpage && $pno > 0) {
                        $paginationStr .= "<button class='join-item btn' onclick=" . $callFunc . "($frm,$to,$pno) >&laquo;</button>";
                    }
                } else {
                    $paginationStr .= "<button class='join-item btn' disabled>&laquo;</button>";
                }
                if ($totalpage >= 1) {
                    $i = 1;
                    $from = 0;
                    $to = $block;
                    while ($i <= $totalpage) {
                        if ($from == 0) {
                            $paginationStr .= "<button class='join-item btn";
                            if ($i == $plink) {
                                $paginationStr .= " btn-active";
                            }
                            $paginationStr .= "' ";
                            $paginationStr .= "onclick=" . $callFunc . "($from,$to,$i)>$i";
                            $paginationStr .= "</button>";
                        } else {
                            $paginationStr .= "<button class='join-item btn";
                            if ($i == $plink) {
                                $paginationStr .= " btn-active";
                            }
                            $paginationStr .= "' ";
                            $paginationStr .= "onclick=" . $callFunc . "($from,$to,$i)>$i";
                            $paginationStr .= "</button>";
                        }
                        $i++;
                        $from = $from + $block;
                        if ($to > $totalrecord) {
                            $to = $totalrecord;
                        }
                    }
                }
                if ($totalrecord > $block) {
                    $f = $from;
                    $page = (int)$page + 1;
                    if ($f == "" || $f == 0) {
                        $f = $block;
                        $page = 2;
                    } else {
                        $f = $f + $block;
                    }
                    $t = $block;
                    if ($t > $totalrecord) {
                        $t = $totalrecord;
                    }
                    if ($page <= $totalpage) {
                        $paginationStr .= "<button class='join-item btn' onclick=" . $callFunc . "($f,$t,$page) >&raquo;</button>";
                    }
                } else {
                    $paginationStr .= "<button class='join-item btn' disabled>&raquo;</button>";
                }

                echo $paginationStr;
                ?>
                <!--                <button-->
                <!--                        class="join-item btn hover:bg-primary hover:text-primary-content"-->
                <!--                >-->
                <!--                    «-->
                <!--                </button>-->
                <!--                <div class="dropdown join-item">-->
                <!--                    <div tabindex="0" role="button" class="btn rounded-none">-->
                <!--                        page 1-->
                <!--                    </div>-->
                <!--                    <ul-->
                <!--                            tabindex="0"-->
                <!--                            class="dropdown-content z-[1] menu shadow bg-base-100 p-0 w-full"-->
                <!--                    >-->
                <!--                        <li><a class="text-center">page 2</a></li>-->
                <!--                        <li><a class="text-center">page 3</a></li>-->
                <!--                        <li><a class="text-center">page 4</a></li>-->
                <!--                        <li><a class="text-center">page 5</a></li>-->
                <!--                    </ul>-->
                <!--                </div>-->
                <!--                <button-->
                <!--                        class="join-item btn hover:bg-primary hover:text-primary-content"-->
                <!--                >-->
                <!--                    »-->
                <!--                </button>-->
            </div>
        </div>
        <div class="text-center font-medium">
            <h2 class="text-xl font-bold mb-3">LC Transaction List</h2>
            <div class="text-lg">Heritage Polymer & Lami Tubes Ltd.</div>
            <div>Gulshan-1, Dhaka, bangladesh</div>
        </div>
        <div class="overflow-x-auto mt-3">
            <table class="table table-zebra">
                <!-- head -->
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Voucher No.</th>
                    <th>Voucher Type</th>
                    <th>Account Name</th>
                    <th>LC Number</th>
                    <th>Debit (BDT)</th>
                    <th>Credit (BDT)</th>
                    <th class="text-center">Created By</th>
                    <!--                    <th class="text-center">Action</th>-->
                </tr>
                </thead>
                <tbody>
                <?php
                // Close the connection
                mysqli_close($connection);


                // Check if the query was successful
                if ($result->num_rows <= 0) {
                    ?>
                    <tr>
                        <td colspan="9" class="text-center">Record not found</td>
                    </tr>
                    <?php
                } else {

                    // Fetch and display the results
                    while ($row = mysqli_fetch_assoc($result)) {
                        $total_dr += $row['dr'];
                        $total_cr += $row['cr'];
                        ?>

                        <tr>
                            <th><?php echo $row['created_date']; ?></th>
                            <td><?php echo $row['voucher_no']; ?></td>
                            <td>
                                <div><?php echo $row['head_type']; ?></div>
                            </td>
                            <td>
                                <div><?php echo $row['sub_head_name']; ?></div>

                            </td>
                            <td>
                                <div><?php echo $row['description']; ?></div>
                            </td>
                            <td>
                                <div><?php echo $row['dr']; ?></div>

                            </td>
                            <td>
                                <div><?php echo $row['cr']; ?></div>

                            </td>
                            <td>
                                <div><?php echo $row['created_by']; ?></div>

                            </td>
                            <!--                            <td>-->
                            <!--                                <div class="flex items-center justify-center gap-1">-->
                            <!--                                    <button class="btn btn-ghost">-->
                            <!--                                        <svg-->
                            <!--                                                xmlns="http://www.w3.org/2000/svg"-->
                            <!--                                                fill="none"-->
                            <!--                                                viewBox="0 0 24 24"-->
                            <!--                                                stroke-width="1.5"-->
                            <!--                                                stroke="currentColor"-->
                            <!--                                                class="w-5 h-5"-->
                            <!--                                        >-->
                            <!--                                            <path-->
                            <!--                                                    stroke-linecap="round"-->
                            <!--                                                    stroke-linejoin="round"-->
                            <!--                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"-->
                            <!--                                            />-->
                            <!--                                        </svg>-->
                            <!--                                    </button>-->
                            <!--                                    <button class="btn btn-ghost">-->
                            <!--                                        <svg-->
                            <!--                                                xmlns="http://www.w3.org/2000/svg"-->
                            <!--                                                fill="none"-->
                            <!--                                                viewBox="0 0 24 24"-->
                            <!--                                                stroke-width="1.5"-->
                            <!--                                                stroke="currentColor"-->
                            <!--                                                class="w-7 h-7"-->
                            <!--                                        >-->
                            <!--                                            <path-->
                            <!--                                                    stroke-linecap="round"-->
                            <!--                                                    stroke-linejoin="round"-->
                            <!--                                                    d="M6 18 18 6M6 6l12 12"-->
                            <!--                                            />-->
                            <!--                                        </svg>-->
                            <!--                                    </button>-->
                            <!--                                </div>-->
                            <!--                            </td>-->
                        </tr>

                        <?php
                    }

                }
                ?>


                <!-- row exmp -->

                <tr>
                    <td
                            colspan="5"
                            class="text-right border-r border-primary capitalize"
                    >
                        total
                    </td>
                    <td><?php echo number_format($total_dr, '2', ".", ",") ?> Tk</td>

                    <td><?php echo number_format($total_cr, '2', ".", ",") ?> Tk</td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>

    const nextPage = (frm, to, page_no) => {
        document.getElementById("from").value = frm;
        document.getElementById("to").value = to;
        document.getElementById("page").value = page_no;

        document.getElementById("lcForm").submit.click();
    }

</script>


</body>
</html>

