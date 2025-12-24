const mysql=require("mysql2");
module.exports=mysql.createPool({
    host:"localhost",
    user:"root",
    password:"",
    database:"emp"
});
