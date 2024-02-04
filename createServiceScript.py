import pandas as pd
import requests
import uuid

# Function to create a payload for each row in the Excel file
def create_payload(row):
    # Generate a unique reference using UUID
    ref = str(uuid.uuid4().hex[:6].upper())

    # Generate a unique label using a prefix and UUID
    label = f"Service_{uuid.uuid4().hex[:6].upper()}"

    # Create the payload with mandatory 'org' value
    payload = {
        "ref": ref,
        "label": row["org"],
        "type": 1,
        "array_options": {
            "org": row["org"]
        }
    }

    # Include other optional fields if they are not empty
    optional_fields = [
        "org_def",
        "city",
        "act_type",
        "address",
        "max_vol",
        "contact",
        "tel",
        "notes"
    ]

    for field in optional_fields:
        if pd.notna(row[field]):
            payload["array_options"][field] = row[field]

    return payload

# Read data from Excel file
excel_file_path = '/Users/i355615/Desktop/val3.xlsx'
df = pd.read_excel(excel_file_path)

# Dolibarr API details
api_url = "https://www.alonandella-dev.site/api/index.php/products"
authorization_key = "<dolibarr's user auth-key>"

# Iterate through rows in the DataFrame and make API calls
for index, row in df.iterrows():
    payload = create_payload(row)

    # Include API key in the URL parameter
    params = {"DOLAPIKEY": authorization_key}

    headers = {
        "Content-Type": "application/json"
    }

    try:
        response = requests.post(api_url, json=payload, params=params, headers=headers)
        response.raise_for_status()

        print(f"API request successful for row {index + 1}. Response:")
        print(response.json())
    except requests.exceptions.HTTPError as errh:
        print(f"HTTP Error for row {index + 1}: {errh}")
        print("Response text:")
        print(response.text)  # Print the response for additional debugging
    except requests.exceptions.RequestException as err:
        print(f"Request Error for row {index + 1}: {err}")
