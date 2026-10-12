#include <ESP8266WiFi.h>
#include <WiFiClientSecure.h>
#include <ESP8266HTTPClient.h>

// Wi-Fi credentials
const char *ssid = "Airtel_56";
const char *password = "Raviuma5658";

// Render PHP URL
const char *serverURL =
  "https://datalogger-canp.onrender.com/postdemo.php";

// Three test values
int adcvalue1 = 0;
int adcvalue2 = 0;
int adcvalue3 = 0;

void setup() {
  Serial.begin(115200);
  delay(1000);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  Serial.println();
  Serial.print("Connecting to Wi-Fi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("Wi-Fi connected");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void loop() {

  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Wi-Fi disconnected");
    delay(5000);
    return;
  }

  WiFiClientSecure client;

  // Diagnostic test only: disables certificate verification.
  // Use proper certificate verification for production.
  client.setInsecure();

  HTTPClient http;

  String postData =
    "value1=" + String(adcvalue1) +
    "&value2=" + String(adcvalue2) +
    "&value3=" + String(adcvalue3);

  Serial.println();
  Serial.println("Sending data to Render:");
  Serial.println(postData);

  if (http.begin(client, serverURL)) {

    http.addHeader(
      "Content-Type",
      "application/x-www-form-urlencoded"
    );

    int httpCode = http.POST(postData);

    Serial.print("HTTP response code: ");
    Serial.println(httpCode);

    if (httpCode > 0) {
      String payload = http.getString();

      Serial.println("Server response:");
      Serial.println(payload);
    } else {
      Serial.print("HTTP error description: ");
      Serial.println(http.errorToString(httpCode));
    }

    http.end();

  } else {
    Serial.println("Could not initialize HTTP connection");
  }

  // Prepare values for the next transmission
  adcvalue1 += 10;
  adcvalue2 += 20;
  adcvalue3 += 30;

  delay(5000);
}
