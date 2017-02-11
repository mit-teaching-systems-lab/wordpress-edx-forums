// Casper configuration
var TIMEOUT = 60000;
var casper = require('casper').create({
  logLevel: 'info',
  verbose: true,
  exitOnError: true,
  onDie: function(c, message, status) { c.echo('Died:', message, status); },
  onError: function(c, message, status) { c.echo('Error:', message, status); },
  stepTimeout: TIMEOUT,
  waitTimeout: TIMEOUT
});


// App configuration
var env = require('system').env
if (!env.USERNAME || !env.PASSWORD) {
  casper.echo('Missing environment variables: BASE_URL, USERNAME, PASSWORD.');
  casper.exit(1);
}
var config = {
  baseUrl: env.BASE_URL,
  username: env.USERNAME,
  password: env.PASSWORD
};


function chooseLink(hrefs) {
  // random:
  // var index = Math.floor(Math.random() * hrefs.length);
  // return hrefs[index];
  return hrefs[0];
}

// Sign in
var adminLogin = '/wp-login.php?redirect_to=' + config.baseUrl + '/admin-login/';
casper.start(config.baseUrl + adminLogin, function() {
  this.echo('Signing in to ' + config.baseUrl + '...');
  this.fill('form', {
    'log': config.username,
    'pwd': config.password
  }, true);

  casper.waitForSelector('#masthead', function() {
    this.echo('Signed in.');
  }, TIMEOUT);
});

// Visit the home page
casper.thenOpen(config.baseUrl);

// Pick a unit
casper.then(function() {
  var href = chooseLink(this.getElementsAttribute('.bbp-forums .forum a', 'href'));
  this.echo('Opening ' + href + '...');
  this.open(href);
});

// Pick a forum
casper.then(function() {
  var href = chooseLink(this.getElementsAttribute('.bbp-forums .forum a', 'href'));
  this.echo('Opening ' + href + '...');
  this.open(href);
});

// Pick a topic
casper.then(function() {
  var href = chooseLink(this.getElementsAttribute('.bbp-topics .topic a', 'href'));
  this.echo('Opening ' + href + '...');
  this.open(href);
});

casper.then(function() {
  this.echo('Done.');
});

casper.run();