<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Word-Font";
            font-size: 10px;
            color: #000;
            margin-top: 0px;
        }

        .content {
            padding-left: 150px;
            text-align: center;
        }

        table {
            /* border-collapse: collapse; */
        }

        .page_break {
            page-break-before: always;
        }

        .middle-table .border td {
            border: 1px solid #000;
            border-bottom: 0px;
            border-right: 0px;
            /* background */
            text-align: center;
        }

        .middle-table {
            border-spacing: 0px;
            /* width:380px; */
        }

        .border-bottom {
            border-bottom: 1px solid #000 !important;
        }
    </style>
</head>

<body class="main">
    <div class="content">
        <table class="middle-table">
            <tr>
                <td align="right" style="width:30px">&ensp;&ensp;&ensp;</td>
                <td align="center" style="padding:0px 15px;width:50px">&ensp;&ensp;&ensp;&ensp;&ensp;</td>
                <td style="padding-left:5px;width:180px;text-align:left !important">&ensp;&ensp;&ensp;&ensp;&ensp;</td>
                <td align="center" style="width:80px">&ensp;&ensp;&ensp;&ensp;&ensp;</td>
            </tr>
            <tr>
                <td colspan="4" align="center" style="width:390px;font-weight:900;font-size:15px">Check-in Report</td>
            </tr>
            <tr>
                <td colspan="4" align="right" style="width:390px;font-size:9px">Date: <?php echo date("Y/m/d"); ?></td>
            </tr>
            <!-- <tr></tr> -->

            <?php if (!empty($data['checked_in'])) : ?>
                <?php $i = 0; ?>
                <tr>
                    <td colspan="4" style="text-align:left;font-style:italic;padding:5px 0px">出 社 状況:</td>
                </tr>
                <tr class="border" style="border-right: 1px solid #000 !important">
                    <td align="center" style="width:20px !important;text-align:center !important;font-weight:900">No.</td>
                    <td align="center" style="text-align:center !important;font-weight:900">ID</td>
                    <td align="center" style="text-align:center !important;font-weight:900">Name</td>
                    <td align="center" style="width:80px !important;border-right:1px solid #000 !important;font-weight:900">Check-in Time</td>
                </tr>
                <?php foreach ($data['checked_in'] as $item) : ?>
                    <?php $i++; ?>
                    <?php if ($i < count($data['checked_in'])) : ?>
                        <tr class="border">
                            <td align="right" style="width:20px !important;text-align:right !important;padding-right:4px"><?php echo $i; ?></td>
                            <td align="center" style="text-align:center !important"><?php echo $item->StaffID; ?></td>
                            <td align="left" style="text-align:left !important;padding-left:5px"><?php echo $item->Tblmstaff->Name; ?></td>
                            <td align="center" style="border-right:1px solid #000 !important"><?php echo date("H:i", strtotime($item->TimeIn)); ?></td>
                        </tr>
                    <?php else : ?>
                        <tr class="border">
                            <td align="right" style="border-bottom:1px solid #000 !important; width:20px !important;text-align:right !important;padding-right:4px"><?php echo $i; ?></td>
                            <td align="center" style="border-bottom:1px solid #000 !important; text-align:center !important"><?php echo $item->StaffID; ?></td>
                            <td align="left" style="border-bottom:1px solid #000 !important; text-align:left !important;padding-left:5px"><?php echo $item->Tblmstaff->Name; ?></td>
                            <td align="center" style="border-bottom:1px solid #000 !important; border-right:1px solid #000 !important"><?php echo date("H:i", strtotime($item->TimeIn)); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td style="padding-bottom:10px;" colspan="4">
                    <td>
                </tr>
            <?php endif; ?>
            <!-- <tr>
                <td align="right" style="padding-left:35px;padding-right:5px">A0002</td>
                <td align="left" style="padding:4px">Tran Van Sang:</td>
                <td align="center">07:45</td>
            </tr> -->


            <?php if (!empty($data['not_come'])) : ?>
                <?php $i = 0; ?>
                <tr>
                    <td colspan="4" align="left" style="padding:5px 0px">Not yet come:</td>
                </tr>
                <tr class="border" style="border-right: 1px solid #000 !important">
                    <td align="center" style="width:20px !important;text-align:center !important;font-weight:900">No.</td>
                    <td align="center" style="text-align:center !important;font-weight:900">ID</td>
                    <td colspan="2" align="center" style="text-align:center !important;border-right:1px solid #000 !important;font-weight:900">Name</td>
                    <!-- <td align="center" style="border-right: 1px solid #000 !important">Check-in Time</td> -->
                </tr>
                <?php foreach ($data['not_come'] as $item) : ?>
                    <?php $i++; ?>
                    <?php if ($i < count($data['not_come'])) : ?>
                        <tr class="border">
                            <td align="right" style="width:20px !important;text-align:right !important;padding-right:4px"><?php echo $i; ?></td>
                            <td align="center" style="text-align:center !important"><?php echo $item->StaffID; ?></td>
                            <td colspan="2" align="left" style="text-align:left !important;padding-left:5px;border-right:1px solid #000 !important"><?php echo $item->Name; ?></td>
                        </tr>
                    <?php else : ?>
                        <tr class="border">
                            <td align="right" style="border-bottom:1px solid #000 !important;width:20px !important;text-align:right !important;padding-right:4px"><?php echo $i; ?></td>
                            <td align="center" style="border-bottom:1px solid #000 !important;text-align:center !important"><?php echo $item->StaffID; ?></td>
                            <td colspan="2" align="left" style="border-bottom:1px solid #000 !important;text-align:left !important;padding-left:5px;border-right:1px solid #000 !important"><?php echo $item->Name; ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <!--<tr>
                <td align="right" style="padding:4px">A0109</td>
                <td colspan="2"></td>
            </tr> -->
        </table>

    </div>

</body>

</html>