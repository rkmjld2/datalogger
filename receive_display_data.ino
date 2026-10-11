
#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <ESP8266WebServer.h>
#include <ESP8266HTTPClient.h>
#include <ArduinoJson.h>

// =====================================================
// WIFI SETTINGS
// =====================================================

const char* ssid = "Airtel_56";
const char* password = "Raviuma5658";

// =====================================================
// PHP DATABASE FILE
// =====================================================

const char* serverURL =
    "http://192.168.1.10/datalogger/getlogs.php";

// =====================================================
// ESP8266 WEB SERVER
// =====================================================

ESP8266WebServer server(80);

// =====================================================
// HTML DATA
// =====================================================

String htmlPage = "";

// =====================================================
// CREATE HTML PAGE
// =====================================================

void createWebPage(JsonDocument& doc)
{
    htmlPage = "";

    // -------------------------------------------------
    // HTML START
    // -------------------------------------------------

    htmlPage += "<!DOCTYPE html>";

    htmlPage += "<html>";

    htmlPage += "<head>";

    htmlPage += "<meta charset='UTF-8'>";

    htmlPage += "<meta name='viewport' ";
    htmlPage += "content='width=device-width, initial-scale=1.0'>";

    htmlPage += "<title>ESP8266 Database Records</title>";

    // -------------------------------------------------
    // AUTOMATIC REFRESH
    // -------------------------------------------------

    htmlPage += "<meta http-equiv='refresh' content='5'>";

    // -------------------------------------------------
    // CSS
    // -------------------------------------------------

    htmlPage += "<style>";

    htmlPage += "body {";
    htmlPage += "font-family: Arial, sans-serif;";
    htmlPage += "background-color: #f2f2f2;";
    htmlPage += "margin: 20px;";
    htmlPage += "}";

    htmlPage += "h1 {";
    htmlPage += "text-align: center;";
    htmlPage += "color: #222;";
    htmlPage += "}";

    htmlPage += ".info {";
    htmlPage += "text-align: center;";
    htmlPage += "font-size: 20px;";
    htmlPage += "margin: 15px;";
    htmlPage += "}";

    htmlPage += ".tablebox {";
    htmlPage += "overflow-x: auto;";
    htmlPage += "}";

    htmlPage += "table {";
    htmlPage += "border-collapse: collapse;";
    htmlPage += "width: 100%;";
    htmlPage += "background-color: white;";
    htmlPage += "}";

    htmlPage += "th {";
    htmlPage += "background-color: #333;";
    htmlPage += "color: white;";
    htmlPage += "padding: 10px;";
    htmlPage += "border: 1px solid #777;";
    htmlPage += "}";

    htmlPage += "td {";
    htmlPage += "padding: 8px;";
    htmlPage += "text-align: center;";
    htmlPage += "border: 1px solid #999;";
    htmlPage += "}";

    htmlPage += "tr:nth-child(even) {";
    htmlPage += "background-color: #eeeeee;";
    htmlPage += "}";

    htmlPage += "</style>";

    htmlPage += "</head>";

    // -------------------------------------------------
    // BODY
    // -------------------------------------------------

    htmlPage += "<body>";

    htmlPage += "<h1>ESP8266 Database Records</h1>";

    // -------------------------------------------------
    // RECORD COUNT
    // -------------------------------------------------

    int recordCount = doc["count"];

    htmlPage += "<div class='info'>";

    htmlPage += "Total Records: ";

    htmlPage += String(recordCount);

    htmlPage += "</div>";

    // -------------------------------------------------
    // TABLE START
    // -------------------------------------------------

    htmlPage += "<div class='tablebox'>";

    htmlPage += "<table>";

    // -------------------------------------------------
    // TABLE HEADER
    // -------------------------------------------------

    htmlPage += "<tr>";

    htmlPage += "<th>ID</th>";

    htmlPage += "<th>Value 1</th>";

    htmlPage += "<th>Value 2</th>";

    htmlPage += "<th>Value 3</th>";

    htmlPage += "<th>Date</th>";

    htmlPage += "<th>Time</th>";

    htmlPage += "<th>Timestamp</th>";

    htmlPage += "</tr>";

    // -------------------------------------------------
    // DATABASE RECORDS
    // -------------------------------------------------

    JsonArray data = doc["data"];

    for (JsonObject row : data)
    {
        htmlPage += "<tr>";

        htmlPage += "<td>";
        htmlPage += row["id"].as<String>();
        htmlPage += "</td>";

        htmlPage += "<td>";
        htmlPage += row["value1"].as<String>();
        htmlPage += "</td>";

        htmlPage += "<td>";
        htmlPage += row["value2"].as<String>();
        htmlPage += "</td>";

        htmlPage += "<td>";
        htmlPage += row["value3"].as<String>();
        htmlPage += "</td>";

        htmlPage += "<td>";
        htmlPage += row["date"].as<String>();
        htmlPage += "</td>";

        htmlPage += "<td>";
        htmlPage += row["time"].as<String>();
        htmlPage += "</td>";

        htmlPage += "<td>";
        htmlPage += row["timestamp"].as<String>();
        htmlPage += "</td>";

        htmlPage += "</tr>";
    }

    // -------------------------------------------------
    // TABLE END
    // -------------------------------------------------

    htmlPage += "</table>";

    htmlPage += "</div>";

    // -------------------------------------------------
    // FOOTER
    // -------------------------------------------------

    htmlPage += "<p style='text-align:center;'>";

    htmlPage += "ESP8266 Web Server";

    htmlPage += "<br>";

    htmlPage += "Page refreshes every 5 seconds";

    htmlPage += "</p>";

    htmlPage += "</body>";

    htmlPage += "</html>";
}


// =====================================================
// GET DATABASE DATA
// =====================================================

void getDatabaseData()
{
    if (WiFi.status() != WL_CONNECTED)
    {
        Serial.println("WiFi not connected");

        return;
    }

    WiFiClient client;

    HTTPClient http;

    Serial.println();
    Serial.println("======================================");
    Serial.println("Requesting ALL records from database...");
    Serial.println("======================================");

    http.begin(client, serverURL);

    int httpCode = http.GET();

    Serial.print("HTTP Code: ");

    Serial.println(httpCode);

    // -------------------------------------------------
    // HTTP SUCCESS
    // -------------------------------------------------

    if (httpCode == HTTP_CODE_OK)
    {
        String payload = http.getString();

        Serial.println();
        Serial.println("Data received from PHP:");
        Serial.println();
        Serial.println(payload);
        Serial.println();

        // -------------------------------------------------
        // JSON MEMORY
        // -------------------------------------------------

        DynamicJsonDocument doc(8192);

        // -------------------------------------------------
        // JSON PARSE
        // -------------------------------------------------

        DeserializationError error =
            deserializeJson(doc, payload);

        if (error)
        {
            Serial.print("JSON error: ");

            Serial.println(error.c_str());

            htmlPage = "<!DOCTYPE html>";

            htmlPage += "<html><body>";

            htmlPage += "<h1>JSON Parsing Error</h1>";

            htmlPage += "<p>";

            htmlPage += error.c_str();

            htmlPage += "</p>";

            htmlPage += "</body></html>";
        }
        else
        {
            const char* status = doc["status"];

            Serial.print("Status: ");

            Serial.println(status);

            if (strcmp(status, "success") == 0)
            {
                int count = doc["count"];

                Serial.print("Number of records: ");

                Serial.println(count);

                // Create HTML
                createWebPage(doc);

                Serial.println();

                Serial.println(
                    "Web page created successfully."
                );

                Serial.print(
                    "HTML page length: "
                );

                Serial.println(htmlPage.length());
            }
            else
            {
                htmlPage = "<!DOCTYPE html>";

                htmlPage += "<html><body>";

                htmlPage += "<h1>Database Error</h1>";

                htmlPage += "<p>";

                if (doc["message"])
                {
                    htmlPage +=
                        doc["message"].as<String>();
                }

                htmlPage += "</p>";

                htmlPage += "</body></html>";
            }
        }
    }
    else
    {
        Serial.print("HTTP Error: ");

        Serial.println(
            http.errorToString(httpCode)
        );

        htmlPage = "<!DOCTYPE html>";

        htmlPage += "<html><body>";

        htmlPage += "<h1>HTTP Error</h1>";

        htmlPage += "<p>HTTP Code: ";

        htmlPage += String(httpCode);

        htmlPage += "</p>";

        htmlPage += "</body></html>";
    }

    http.end();
}


// =====================================================
// SEND WEB PAGE TO BROWSER
// =====================================================

void handleRoot()
{
    Serial.println();
    Serial.println("==============================");
    Serial.println("BROWSER REQUEST RECEIVED");
    Serial.println("==============================");

    Serial.print("HTML length = ");

    Serial.println(htmlPage.length());

    // -------------------------------------------------
    // IF NO HTML
    // -------------------------------------------------

    if (htmlPage.length() == 0)
    {
        server.send(
            200,
            "text/html",
            "<!DOCTYPE html>"
            "<html>"
            "<body>"
            "<h1>No data available</h1>"
            "</body>"
            "</html>"
        );

        return;
    }

    // -------------------------------------------------
    // SEND HTTP HEADER
    // -------------------------------------------------

    server.setContentLength(
        htmlPage.length()
    );

    server.send(
        200,
        "text/html",
        ""
    );

    // -------------------------------------------------
    // SEND HTML IN SMALL CHUNKS
    // -------------------------------------------------

    const int chunkSize = 200;

    int position = 0;

    while (position < htmlPage.length())
    {
        int remaining =
            htmlPage.length() - position;

        int size =
            min(chunkSize, remaining);

        String chunk =
            htmlPage.substring(
                position,
                position + size
            );

        server.sendContent(chunk);

        position += size;

        yield();
    }

    Serial.println("HTML sent to browser.");

    Serial.println("==============================");
}


// =====================================================
// SETUP
// =====================================================

void setup()
{
    delay(1000);

    Serial.begin(115200);

    Serial.println();
    Serial.println();
    Serial.println("======================================");
    Serial.println("ESP8266 DATABASE RECEIVER");
    Serial.println("======================================");

    // -------------------------------------------------
    // WIFI
    // -------------------------------------------------

    WiFi.mode(WIFI_STA);

    WiFi.begin(
        ssid,
        password
    );

    Serial.println();

    Serial.print("Connecting to WiFi");

    while (WiFi.status() != WL_CONNECTED)
    {
        delay(500);

        Serial.print(".");
    }

    Serial.println();

    Serial.println();

    Serial.println("WiFi Connected");

    Serial.print("ESP8266 IP Address: ");

    Serial.println(
        WiFi.localIP()
    );

    // -------------------------------------------------
    // GET DATABASE RECORDS
    // -------------------------------------------------

    getDatabaseData();

    // -------------------------------------------------
    // WEB SERVER ROUTES
    // -------------------------------------------------

    server.on(
        "/",
        HTTP_GET,
        handleRoot
    );

    // -------------------------------------------------
    // START WEB SERVER
    // -------------------------------------------------

    server.begin();

    Serial.println();

    Serial.println(
        "Web server started."
    );

    Serial.print(
        "Open browser at: http://"
    );

    Serial.print(
        WiFi.localIP()
    );

    Serial.println("/");
}


// =====================================================
// LOOP
// =====================================================

void loop()
{
    // -------------------------------------------------
    // HANDLE BROWSER
    // -------------------------------------------------

    server.handleClient();

    // -------------------------------------------------
    // UPDATE DATABASE EVERY 5 SECONDS
    // -------------------------------------------------

    static unsigned long lastUpdate = 0;

    if (millis() - lastUpdate >= 5000)
    {
        lastUpdate = millis();

        getDatabaseData();
    }
}
