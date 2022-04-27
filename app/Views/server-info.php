<?php helper("morse"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container col-12">
        <div class="col-12 mt-3">
            <h1>Server Status</h1>
        </div>
        <table class="table col-12 table-striped table-hover">
            <tr>
                <th>Services</th>
                <th>Port</th>
                <th>Status</th>
            </tr>
            <?php foreach ($services as $service) : ?>
                <tr>
                    <td><?= morseToString($service["service"], $morseDictionary) ?></td>
                    <td><?= morseToString($service["port"], $morseDictionary) ?></td>
                    <td class="<?= morseToString($service["status"],$morseDictionary) == 'online' ? 'text-success' : 'text-danger' ?>"><?= morseToString($service["status"], $morseDictionary) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="col-12 mt-3">
            <h1>Server Informations</h1>
        </div>
        <table class="table col-12 table-striped table-hover">
            <tr>
                <td>Disk Space</td>
                <td><?= morseToString($freeDiskSpace,$morseDictionary) . " MB / " . morseToString($totalDiskSpace,$morseDictionary)." MB" ?></td>
            </tr>
            <tr>
                <td>Ram Usage</td>
                <td><?= morseToString($ramUsage,$morseDictionary) . " MB / " . morseToString($totalRam,$morseDictionary)." MB" ?></td>
            </tr>
            <tr>
                <td>CPU Usage</td>
                <td>%<?= morseToString($cpuUsage,$morseDictionary) ?></td>
            </tr>
        </table>
    </div>

</body>

</html>