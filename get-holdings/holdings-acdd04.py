import requests

url = 'https://prd-ap-fund-sc.allianzgi.com/api/fund/fundtopholdingwithvariations'

headers = {
    'Accept': 'application/json, text/javascript, */*; q=0.01',
    'Accept-Language': 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
    'Cache-Control': 'no-cache',
    'Connection': 'keep-alive',
    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
}

data = {
    'SalesChannel': 'tw_endclients',
    'ShareClassId': '',
    'SeoName': 'allianz-global-investors-taiwan-technology-fund',
    'DatasourceId': '{41DAD5F5-A5D3-47D4-87F7-447EE9D5F66A}',
    'Language': 'zh-TW',
    'IsLocalLanguage': 'False',
    'IsAktivDepot': 'False',
    'OverrideSiteSettings': 'False',
    'IsuserLoggedIn': 'False',
    'IsuserAdmin': 'False',
    'RoleID': 'd100975e-188a-4957-8c53-7113125192c7',
    'Region': 'ap',
}

response = requests.post(url, headers=headers, data=data)

# Check if the response was successful
if response.status_code == 200:
    json_response = response.json()
    data_list = json_response.get('DataList', None)

    if data_list:
        for item in data_list:
            print(f"Top Holdings for {item['label']} (As Of: {item['AsOfDateValue']}):")
            for holding in item['TopHoldingValuesList']:
                if holding['ListValue']:
                    detail = holding['ListValue'][2]
                    print(f"  {holding['label']}  - {detail['Key']}: {detail['Value']}")
            #print("\n" + "-"*50 + "\n")  # Separator for readability
    else:
        print("No data found in DataList.")
else:
    print(f"Error: {response.status_code}")

