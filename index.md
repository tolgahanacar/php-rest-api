## Welcome,

You can use the [editor on GitHub](https://github.com/tolgahanacar/php-rest-api/edit/gh-pages/index.md) to maintain and preview the content for your website in Markdown files.

Whenever you commit to this repository, GitHub Pages will run [Jekyll](https://jekyllrb.com/) to rebuild the pages in your site, from the content in your Markdown files.

### Database structure

Markdown is a lightweight and easy-to-use syntax for styling your writing. It includes conventions for

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

### request.rest
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
### Postman
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
```markdown
Syntax highlighted code block

# Header 1
## Header 2
### Header 3

- Bulleted
- List

1. Numbered
2. List

**Bold** and _Italic_ and `Code` text

[Link](url) and ![Image](src)
```

For more details see [GitHub Flavored Markdown](https://guides.github.com/features/mastering-markdown/).

### Jekyll Themes

Your Pages site will use the layout and styles from the Jekyll theme you have selected in your [repository settings](https://github.com/tolgahanacar/php-rest-api/settings/pages). The name of this theme is saved in the Jekyll `_config.yml` configuration file.

### Support or Contact

Having trouble with Pages? Check out our [documentation](https://docs.github.com/categories/github-pages-basics/) or [contact support](https://support.github.com/contact) and we’ll help you sort it out.
