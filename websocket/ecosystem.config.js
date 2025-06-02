module.exports = {
  apps: [{
    name: 'grch-messenger-server',
    script: './server.js',
    instances: 1,
    max_memory_restart: '200M',
    max_restarts: 10,
    min_uptime: 10000, // 10 seconds
    watch: true,
    ignore_watch: ['node_modules', 'logs'],
    error_file: './logs/error.log',
    out_file: './logs/output.log',
    merge_logs: true,
    time: true
  }]
};