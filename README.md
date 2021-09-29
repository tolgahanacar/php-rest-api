# php-rest-api
PHP Rest(Restful) API
<h2>Database structure</h2>
<table>
  <tr>
    <th>id</th>
    <th>username</th>
    <th>first_name</th>
    <th>last_name</th>
    <th>email</th>
    <th>updateDate</th>
    <th>createDate</th>
  </tr>
  <tr>
    <td>int [AUTO_INCREMENT]</td>
    <td>varchar(60)</td>
    <td>varchar(50)</td>
    <td>varchar(50)</td>
    <td>varchar(60)</td>
    <td>TIMESTAMP [CURRENT_TIMESTAMP]</td>
    <td>TIMESTAMP</td>
  </tr>
</table>

## request.rest
```rest
// GET Method

GET http://localhost/rest-api/index.php HTTP/1.1

// Single GET Method

GET http://localhost/rest-api/index.php?id=101 HTTP/1.1

// POST Method

POST http://localhost/rest-api/index.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

username=tolgahan01
&first_name=tolgahan
&last_name=acar 
&email=tolga@tolgahanacar.net

// DELETE Method

DELETE http://localhost/rest-api/index.php?id=102 HTTP/1.1

# // PUT Method

PUT http://localhost/rest-api/index.php HTTP/1.1
content-type: application/json

{
    "id": 101,
    "username": "tolgahan02",
    "first_name": "tolgahan",
    "last_name": "acar",
    "email": "info@tolgahanacar.net"
}

```

## Postman
<table>
  <tr>
    <th>Request</th>
    <th>Params</th>
    <th>Query</th>
    <th>form-data</th>
    <th>x-www-form-urlencoded</th>
  </tr>
  <tr>
    <td>GET</td>
    <td>id=1</td>
    <td>http://localhost/rest-api/index.php</td>
    <td>none</td>
    <td>none</td>
  </tr>
  <tr>
    <td>POST</td>
    <td>none</td>
    <td>http://localhost/rest-api/index.php</td>
    <td>username=tolgahan</td>
    <td>none</td>
  </tr>
  <tr>
    <td>PUT</td>
    <td>none</td>
    <td>http://localhost/rest-api/index.php</td>
    <td>none</td>
    <td>{"id":1, "username":"tolgahan0"}</td>
  </tr>
  <tr>
    <td>DELETE</td>
    <td>id=100</td>
    <td>http://localhost/rest-api/index.php</td>
    <td>none</td>
    <td>none</td>
  </tr>
</table>







