var sslRedirect = require('heroku-ssl-redirect');
var express = require('express');
var app = express();

// enable ssl redirect
app.use(sslRedirect());

app.get('/', function(req, res){
  res.send('hello world');
});

app.listen(3000);