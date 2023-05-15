<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Module</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        form * {
            margin-bottom: 1vh;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li>
                <a href="/module">Modules</a>
            </li>
            <li>
                <a href="/project">Projects</a>
            </li>
            <li>
                <a href="/apikey">Api Keys</a>
            </li>
        </ul>
    </nav>
    <h1>Add Module</h1>
    <form enctype="multipart/form-data" action="/module/create" method="POST">
        @csrf
        <label for="file">ZIP File:</label>
        <input name="file" id="file" type="file">
        <input type="submit" value="Submit">
    </form>
</body>
</html>