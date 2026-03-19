// ===================================
// Backend Connection Checker
// ===================================
// This script helps diagnose network connectivity issues

const os = require("os");
const http = require("http");

console.log("\n🔍 BACKEND CONNECTION DIAGNOSTICS\n");
console.log("=".repeat(50));

// 1. Get local IP addresses
console.log("\n1️⃣  YOUR LOCAL IP ADDRESSES:");
console.log("-".repeat(50));
const interfaces = os.networkInterfaces();
for (const name of Object.keys(interfaces)) {
  for (const iface of interfaces[name]) {
    if (iface.family === "IPv4" && !iface.internal) {
      console.log(`   ${name}: ${iface.address}`);
    }
  }
}

// 2. Check if backend is reachable
console.log("\n2️⃣  TESTING BACKEND CONNECTIVITY:");
console.log("-".repeat(50));

const testUrls = [
  "http://192.168.1.14:8000/api",
  "http://localhost:8000/api",
  "http://127.0.0.1:8000/api",
];

function testConnection(url) {
  return new Promise((resolve) => {
    const urlObj = new URL(url);
    const options = {
      hostname: urlObj.hostname,
      port: urlObj.port,
      path: urlObj.pathname,
      method: "GET",
      timeout: 3000,
    };

    const req = http.request(options, (res) => {
      console.log(`   ✅ ${url} - Status: ${res.statusCode}`);
      resolve(true);
    });

    req.on("error", (err) => {
      console.log(`   ❌ ${url} - Error: ${err.message}`);
      resolve(false);
    });

    req.on("timeout", () => {
      console.log(`   ⏱️  ${url} - Timeout (server not responding)`);
      req.destroy();
      resolve(false);
    });

    req.end();
  });
}

async function runTests() {
  for (const url of testUrls) {
    await testConnection(url);
  }

  console.log("\n3️⃣  RECOMMENDATIONS:");
  console.log("-".repeat(50));
  console.log("   • Make sure Laravel backend is running:");
  console.log("     cd backend && php artisan serve --host=0.0.0.0");
  console.log("   • Update frontend/config/env.ts with the correct IP");
  console.log("   • If testing on physical device, use your machine's IP");
  console.log(
    "   • If testing on emulator, use 10.0.2.2 (Android) or localhost (iOS)",
  );
  console.log("\n" + "=".repeat(50) + "\n");
}

runTests();
