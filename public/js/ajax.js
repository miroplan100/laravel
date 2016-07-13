
var HttpRequest = new window.XMLHttpRequest();

var func = {

query: function (q) {

HttpRequest.open(q.method,q.url,true);

HttpRequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

HttpRequest.setRequestHeader('X-Requested-With','XMLHttpRequest');

HttpRequest.send(func.serialize(q));

return HttpRequest; 

},

input: function (obj) {

var login = document.querySelector('input#login');

var password = document.querySelector('input#password');

if (login.value.length > 0 && password.value.length > 0) {

obj.disabled = true;

this.query({

method: 'POST',

url: 'input',

act: 'check',

login: encodeURIComponent(login.value),

password: encodeURIComponent(password.value)

}).onload = function () {

if (this.status == 200 && this.readyState == 4) {

var r = JSON.parse(this.responseText);

if (r.exist) {

window.document.cookie = 'user=' + r.id + '; expires=' + (new Date(2020,05,10).toGMTString());

window.location.replace('admin');

} else {

document.getElementById('input.info').textContent = 'Логин или пароль не верный.';

}

obj.disabled = false;

}

this.abort();

}

}

},

exit: function () {

window.document.cookie = 'user=; expires=' + (new Date(2010,05,10).toGMTString());

window.location.replace('input');

},

serialize: function (q) {

var arr = [];

for (var i in q) arr.push(i + '=' + q[i]);

return arr.join('&');
	
}

}