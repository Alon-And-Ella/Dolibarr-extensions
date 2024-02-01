const axios = require('axios');
const FormData = require('form-data');
const fs = require('fs');
const path = require('path');
const {username, password, cpanelHost} = require("./.cred.json");

// Set up the API endpoint and credentials
const apiUrl = `https://${cpanelHost}/execute/Fileman/upload_files`;

// Function for single file upload
async function uploadFile(localFilePath, destinationDir) {
  const formData = new FormData();
  formData.append('dir', destinationDir);
  formData.append('file-1', fs.createReadStream(localFilePath));
  formData.append('overwrite', "1")

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
      if (response.data.errors?.length > 0) {
        // file failed to upload - usually because it already exists - try delete an re-upload
        return false;
      }

      console.log('Success:', localFilePath, destinationDir);
      return true;
    } else {
      console.error('Error uploading file:', response.data.errors[0]);
      return false;
    }
  } catch (error) {
    // Handle any errors
    console.error('An error occurred:', error);
    return false;
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
//uploadFolderRecursive('./report/', 'public_html/report/');

//uploadFolderRecursive('./AlonAndElla/', 'public_html/custom/alonandella/');
//uploadFile('./AlonAndElla/webroot/composerExtractor.php', 'public_html/report/webroot/');
//uploadFile('./AlonAndElla/composer.json', 'public_html/report/');
//uploadFile('./report/simple.php', 'public_html/report/');

uploadFile('./AlonAndElla/class/actions_alonandella.class.php', 'public_html/custom/alonandella/class');
//uploadFile('./AlonAndElla/core/modules/modAlonAndElla.class.php', 'public_html/custom/alonandella/core/modules');
//uploadFile('./AlonAndElla/core/triggers/interface_99_modAlonAndElla_MyTrigger.class.php', 'public_html/custom/alonandella/core/triggers');
//uploadFile('./AlonAndElla/core/boxes/my_tasks_box.php', 'public_html/custom/alonandella/core/boxes');