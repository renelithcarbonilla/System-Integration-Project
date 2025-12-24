const express = require("express");
const router = express.Router();
const db = require("../db");
const auth = require("../middleware/auth");

router.get("/payroll", auth, (req, res) => {
  const { employee_id, start, end } = req.query;

  const sql = `
    SELECT 
      SUM(total_hours) AS total_hours,
      SUM(CASE WHEN total_hours > 8 THEN total_hours - 8 ELSE 0 END) AS overtime_hours,
      SUM(late_minutes) AS total_late
    FROM attendance
    WHERE employee_id = ?
    AND date BETWEEN ? AND ?
  `;

  db.query(sql, [employee_id, start, end], (err, result) => {
    if (err) return res.status(500).json(err);

    const data = result[0];
    const late_deduction = data.total_late * 1; // ₱1 per minute

    res.json({
      total_hours: data.total_hours || 0,
      overtime_hours: data.overtime_hours || 0,
      late_deduction
    });
  });
});

module.exports = router;
