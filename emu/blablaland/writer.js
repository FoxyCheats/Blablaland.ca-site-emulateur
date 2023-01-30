let maps = require("./maps/maps.js");
let result = "INSERT INTO map (id,nom) VALUES";
let fs = require('fs');
for(let id in maps) {
    result += `\n(${maps[id][0]},"${maps[id][1]}"),`;
}
fs.writeFileSync("maps.sql", result);