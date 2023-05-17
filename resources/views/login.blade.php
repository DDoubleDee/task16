<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{URL::to('/')}}/css.css">
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
    <h1>Login</h1>
    <a id="createnew" href="/register">Register New User</a>
    <form action="/login" method="POST">
        @csrf
        <div id="row">
            <div>
                <label for="email">Email:</label>
                <input name="email" id="email" type="text" required placeholder="email">
            </div>
            <div>
                <label for="password">Password:</label>
                <input name="password" id="password" type="password" required placeholder="password">
            </div>
        </div>
        <input id="submit" type="submit" value="Submit">
    </form>
</body>
</html>