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
            padding-left: 40px;
        }

        table {
            border-collapse: collapse;
        }

        .page_break {
            page-break-before: always;
        }

        .middle-table .border td {
            border: 0px solid #000;
            text-align: center;
        }

        .middle-table {
            border-spacing: 0px;
        }
    </style>
</head>

<body class="main">
    <div class="content">
        <table class="middle-table" cellspacing="0" cellpadding="0">
            <tr>
                <td align="right" style="padding-left:35px;padding-right:5px">&ensp;&ensp;&ensp;&ensp;&ensp;   </td>
                <td align="left" style="padding:4px">&ensp;&ensp;&ensp;&ensp;&ensp;</td>
                <td align="center">&ensp;&ensp;&ensp;&ensp;&ensp;</td>
            </tr>
            <?php if (!empty($data['checked_in'])) : ?>
                <tr>
                    <td colspan="3" style="text-align:left;font-style:italic">出 社 状況</td>
                </tr>
                <?php foreach ($data['checked_in'] as $item) : ?>
                    <tr>
                        <td align="right" style="padding-left:35px;padding-right:5px"><?php echo $item->StaffID; ?></td>
                        <td align="left" style="padding:4px"><?php echo $item->Tblmstaff->Name; ?>:</td>
                        <td align="center"><?php echo date("H:i", strtotime($item->TimeIn)); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td style="padding-bottom:30px;" colspan="3">
                    <td>
                </tr>
            <?php endif; ?>
            <!-- <tr>
                <td align="right" style="padding-left:35px;padding-right:5px">A0002</td>
                <td align="left" style="padding:4px">Tran Van Sang:</td>
                <td align="center">07:45</td>
            </tr> -->


            <?php if (!empty($data['not_come'])) : ?>
                <tr>
                    <td colspan="3" align="left" style="width:100px">Not yet come:</td>
                </tr>
                <?php foreach ($data['not_come'] as $item) : ?>
                    <tr>
                        <td align="right" style="padding:4px"><?php echo $item->StaffID; ?></td>
                        <td colspan="2"></td>
                    </tr>
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