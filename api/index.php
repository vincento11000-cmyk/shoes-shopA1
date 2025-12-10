const { createServer } = require('http');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

module.exports = async (req, res) => {
  // Serve static files from public directory
  const publicPath = path.join(__dirname, '..', 'public');
  const urlPath = req.url === '/' ? '/index.html' : req.url;
  const filePath = path.join(publicPath, urlPath);
  
  if (fs.existsSync(filePath)) {
    const ext = path.extname(filePath);
    const contentType = {
      '.html': 'text/html',
      '.css': 'text/css',
      '.js': 'application/javascript',
      '.json': 'application/json',
      '.png': 'image/png',
      '.jpg': 'image/jpeg',
      '.jpeg': 'image/jpeg',
      '.gif': 'image/gif',
      '.svg': 'image/svg+xml'
    }[ext] || 'text/plain';
    
    res.setHeader('Content-Type', contentType);
    fs.createReadStream(filePath).pipe(res);
  } else {
    // For Laravel routes, return the Laravel HTML
    const laravelHtml = fs.readFileSync(path.join(publicPath, 'index.html'), 'utf8');
    res.setHeader('Content-Type', 'text/html');
    res.end(laravelHtml);
  }
};