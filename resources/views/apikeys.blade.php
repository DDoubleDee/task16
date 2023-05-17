<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Api Keys</title>
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
    <h1>Api Keys</h1>
    <a id="createnew" href="/apikey/create">Create New Api Key</a>
    <table>
        <tr id="head">
            <td>
                Name
            </td>
            <td>
                Key
            </td>
            <td>
            </td>
        </tr>
        @foreach ($apikeys as $apikey)
            <tr>
                <td>
                    {{$apikey->name}}
                </td>
                <td>
                    {{$apikey->akey}}
                </td>
                <td>
                    <a href="/apikey/delete/{{$apikey->id}}">Delete</a>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>