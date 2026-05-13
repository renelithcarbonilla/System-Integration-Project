const express = require("express");
const cors = require("cors");
const attendanceRoutes = require("./routes/attendance");

const app = express();
app.use(cors());
app.use(express.json());

// Root route
app.get("/", (req, res) => {
  res.send("API is running!");
});

// Use attendance routes
app.use("/api/attendance", attendanceRoutes);

const PORT = 3306;
app.listen(PORT, () => console.log(`API running on http://localhost:${PORT}`));
