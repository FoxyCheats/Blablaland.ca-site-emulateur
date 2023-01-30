var express = require('express');
var app = express();
var https = require('http');
var http = https.Server(app);
var bodyParser = require("body-parser");
var session = require('express-session');
var fs = require('fs');
const replace = require('buffer-replace');
var zlib = require('zlib');
var ServerBBL = require('./blablaland/blablaland.js');
let skins = JSON.parse(fs.readFileSync("skins.json"));
const ipfilter = require('express-ipfilter').IpFilter
var request = require('request');
let conf = {};
conf.data = {};
conf.badword = JSON.parse(fs.readFileSync("badword.json"));
conf.data.token = {};
conf.data.navalId = 0;
conf.data.naval = {};
conf.data.external = {};
var MySql = require('sync-mysql');
conf.data.ip = [];
conf.data.name = "154.49.216.178";

var con = new MySql({
    host: "localhost",
    user: "foxy",
    password: "S9979gDwhaRq6Xx93NuG",
    database: "bbl"
});

let query = function(param1) {
    try {
        return con.query(param1);
    } catch (e) {
        console.log(e);
        return null;
    }
}

process.on('uncaughtException', function(exception) {
    console.log(exception);
});

var port = 80;

var origine = new ServerBBL(12301, conf);
var legende = new ServerBBL(12302, conf);
var fury = new ServerBBL(12303, conf);
origine.database = query;
legende.database = query;
fury.database = query;
console.log('-> Server \t OK!');