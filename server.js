const jsonServer = require("json-server");
const server = jsonServer.create();
const router = jsonServer.router("data/db.json"); // db.json file Path
const middlewares = jsonServer.defaults();

// Add custom CORS middleware
server.use((req, res, next) => {
    res.setHeader("Access-Control-Allow-Origin", "*");
    res.setHeader(
        "Access-Control-Allow-Methods",
        "GET, POST, PUT, DELETE, PATCH, OPTIONS"
    );
    res.setHeader(
        "Access-Control-Allow-Headers",
        "Origin, X-Requested-With, Content-Type, Accept"
    );
    next();
});

server.use(middlewares);
server.use(router);
server.listen(3000, () => {
    console.log("JSON Server is running on http://localhost:3000");
});
