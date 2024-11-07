import requests
from bs4 import BeautifulSoup

# Define the URL
url = 'https://www.ezmoney.com.tw/funds/assets/36'

# Send a GET request to the URL
response = requests.get(url)

# Check if the request was successful
if response.status_code == 200:
    # Parse the HTML content
    soup = BeautifulSoup(response.text, 'html.parser')

    # Fetch the table using its ID
    table = soup.find('table', id='chartTable4')

    # Extract the rows from the table
    rows = table.find_all('tr')

    # Extract and print the content
    table_content = []
    for row in rows:
        cols = row.find_all('td')  # Get columns in each row
        cols_text = [col.get_text(strip=True) for col in cols]  # Extract text from each column
        if cols_text:  # Avoid adding empty rows
            table_content.append(cols_text)

    # Print the extracted table content
    for content in table_content:
        print(content)
else:
    print(f"Failed to retrieve data: {response.status_code}")

