<?php
/**
 * Get available payment methods from Tripay
 * This helps identify which methods are actually enabled on your account
 */

// Reference: https://tripay.co.id/developer
// Endpoint: https://tripay.co.id/api-sandbox/merchant/payment-method

// Sample request:
// curl --location 'https://tripay.co.id/api-sandbox/merchant/payment-method' \
// --header 'Authorization: Bearer YOUR_API_KEY'

// Expected response format:
/*
{
    "success": true,
    "message": "List of payment methods available to the merchant",
    "data": [
        {
            "code": "BCAVA",
            "name": "BCA Virtual Account",
            "type": "BANK",
            "minimum": 10000,
            "maximum": 999999999,
            "fee_merchant": 0,
            "fee_customer": 0
        },
        ...
    ]
}
*/

// Based on logs, your Tripay account seems to have:
// ✅ QRIS
// ✅ BCAVA, BRIVA, BNIVA, MANDIRIVA, etc (Virtual Accounts)
// ❌ DANACASH (NOT AVAILABLE - "Payment method is not exists!")
// ❌ OVOBANK (NOT AVAILABLE - "Payment method is not exists!")

// To fix this, you need to:
// 1. Login to https://tripay.co.id/
// 2. Go to Payment Methods
// 3. Enable DANACASH and OVOBANK (or check if they're available in your plan)
// 4. Wait for activation

// Temporary workaround:
// Remove DANACASH and OVOBANK from the payment methods list until they're activated
