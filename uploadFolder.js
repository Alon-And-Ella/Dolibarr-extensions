const axios = require('axios');
const FormData = require('form-data');
const fs = require('fs');
const path = require('path');
const {username, password} = require("./.cred.json");

// Set up the API endpoint and credentials
const apiUrl = `https://${cpanelHost}/execute/Fileman/upload_files`;

// Function for single file upload
async function uploadFile(localFilePath, destinationDir) {
  const formData = new FormData();
  formData.append('dir', destinationDir);
  formData.append('file-1', fs.createReadStream(localFilePath));

  // Set up the request config
  const config = {
    auth: {
      username,
      password,
    },
    headers: {
      ...formData.getHeaders(),
    },
  };

  try {
    // Make the POST request using axios
    const response = await axios.post(apiUrl, formData, config);
    if (response.status == 200) {
      console.log('Success:', localFilePath, destinationDir);
    } else {
      console.error('Error uploading file:', response.data.errors[0]);
    }
  } catch (error) {
    // Handle any errors
    console.error('An error occurred:', error);
  }
}

// Function for uploading a recursive folder structure
async function uploadFolderRecursive(localFolderPath, destinationDir) {
  try {
    // Get all files and directories in the local folder
    const items = fs.readdirSync(localFolderPath);

    // Iterate through each item
    for (const item of items) {
      const itemPath = path.join(localFolderPath, item);
      const stat = fs.statSync(itemPath);

      // If the item is a directory, recursively upload its contents
      if (stat.isDirectory()) {
        const subDestinationDir = path.join(destinationDir, item);
        await uploadFolderRecursive(itemPath, subDestinationDir);
      } else {
        // If the item is a file, upload it
        await uploadFile(itemPath, destinationDir);
      }
    }
  } catch (error) {
    // Handle any errors
    console.error('An error occurred:', error);
  }
}

// Usage examples
// Single file upload
//uploadFile('./report/main.php', 'public_html/htdocs/report/');

// Recursive folder upload
uploadFolderRecursive('./report/', 'public_html/htdocs/report/');
