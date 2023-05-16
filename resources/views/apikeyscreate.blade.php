<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Api</title>
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
            <li>
                <a href="/logout">Log Out</a>
            </li>
        </ul>
    </nav>
    <h1>Create Api Key</h1>
    <form action="/apikey/create" method="POST">
        @csrf
        <label for="name">Name:</label>
        <input name="name" id="name" type="text" required placeholder="name">
        <input type="submit" value="Submit">
    </form>
</body>
</html>