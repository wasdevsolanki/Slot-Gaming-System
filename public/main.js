const { app, BrowserWindow } = require('electron');
// remove ipcMain from above line
const path = require('path');
const fs = require('fs');

app.commandLine.appendSwitch('disable-web-security');
app.commandLine.appendSwitch('disable-gpu');
// app.commandLine.appendSwitch('disable-software-rasterizer');
function createWindow() {

  const win = new BrowserWindow({
    width: 1366,
    height: 768,
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true, // its true
      enableRemoteModule: false,
      preload: path.join(__dirname, 'preload.js'),
    }

  });
  win.webContents.openDevTools();
  win.loadURL('https://face.dznsolutions.com/admin/login');
  // win.maximize();




  // const filePath = 'C:/Users/Public/secure/secure.txt';

  // readFileContent(filePath)
  //   .then((fileContent) => {

  //     import('node-fetch').then((fetch) => {

  //       fetch.default('https://face.dznsolutions.com/api/validate-dzn', {
  //         method: 'POST',
  //         headers: {
  //           'Content-Type': 'application/json',
  //         },
  //         body: JSON.stringify({ file: fileContent }),
  //       })
  //         .then(response => {
  //           if (!response.ok) {
  //             win.loadFile('index.html');
  //             throw new Error('Network response was not ok');
  //           }
  //           return response.json();
  //         })
  //         .then(data => {


  //           // Handle the response data here
  //           if (data.status == 'true') {
  //             win.loadURL('https://face.dznsolutions.com/admin/login');
  //           } else {
  //             win.loadFile('index.html');
  //           }

  //         })
  //         .catch(error => {
  //           console.error('Error:', error);
  //         });

  //     }).catch((error) => {
  //       console.error('Error importing node-fetch:', error);
  //     });

  //   })
  //   .catch((error) => {
  //     console.error('Error reading file:', error);
  //   });

}

// Function to read file content asynchronously
// async function readFileContent(filePath) {
//   try {
//     const data = await fs.promises.readFile(filePath, 'utf-8');
//     return data;
//   } catch (error) {
//     throw error;
//   }
// }

app.whenReady().then(() => {
  createWindow();

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});
