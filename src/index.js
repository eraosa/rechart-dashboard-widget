import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend } from 'recharts';
import './styles.css';

const DashboardWidget = () => {
  const [data, setData] = useState([]);
  const [period, setPeriod] = useState('7');

  useEffect(() => {
    fetch(`${rechartAPI.root}rechart/v1/data/${period}`, {
      headers: { 'X-WP-Nonce': rechartAPI.nonce }
    })
      .then(response => response.json())
      .then(data => setData(data));
  }, [period]);

  return (
    <div className="dashboard-widget">
      <h2 className="widget-title">Rechart Dashboard Widget</h2>
      <div className="widget-content">
        <select
          onChange={(e) => setPeriod(e.target.value)}
          value={period}
          className="period-select"
        >
          <option value="7">Last 7 days</option>
          <option value="15">Last 15 days</option>
          <option value="30">Last 1 month</option>
        </select>
        <div className="chart-container">
          <LineChart
            width={400}
            height={300}
            data={data}
            className="line-chart"
          >
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="date" />
            <YAxis />
            <Tooltip />
            <Legend />
            <Line type="monotone" dataKey="value" stroke="#8884d8" activeDot={{ r: 8 }} />
          </LineChart>
        </div>
      </div>
    </div>
  );
};

ReactDOM.render(<DashboardWidget />, document.getElementById('rechart-dashboard-widget-root'));