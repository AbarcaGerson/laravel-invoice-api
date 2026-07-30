# Laravel Invoice API

Business API for creating invoices and preparing them for async processing.

## Responsibility

This service owns invoice creation and invoice status.

It will eventually:

1. Receive invoice creation requests.
2. Save invoices and invoice items in MySQL.
3. Publish an `invoice.created` message to SQS.
4. Respond quickly with a `pending` invoice status.
5. Expose endpoints to check invoice status.

It does not generate files and it does not store files in S3 directly. Those responsibilities belong to the future worker and the existing storage API.

## Local Services

| Service | Container | Host Port | Internal Host |
| --- | --- | --- | --- |
| Laravel API | `invoice_api` | `8002` | `api:8000` |
| MySQL | `invoice_api_mysql` | `3309` | `db:3306` |

## First Setup

```bash
cp .env.example .env
docker compose up --build -d
docker compose exec api php artisan key:generate
docker compose exec api php artisan migrate
```

Then open:

```text
http://localhost:8002
```

## API Endpoints

Health check:

```http
GET /api/health
```

List invoices:

```http
GET /api/invoices
```

Create invoice:

```http
POST /api/invoices
Content-Type: application/json
```

Request body:

```json
{
  "document_type": "01",
  "operation_type": "0101",
  "series": "F001",
  "number": 1,
  "issue_date": "2026-07-29",
  "due_date": "2026-08-05",
  "customer_document_type": "6",
  "customer_document_number": "20123456789",
  "customer_name": "Cliente Demo SAC",
  "customer_email": "cliente@example.com",
  "currency": "PEN",
  "items": [
    {
      "product_code": "SERV-001",
      "description": "Servicio de consulta veterinaria",
      "unit_code": "NIU",
      "quantity": 2,
      "unit_price": 100,
      "tax_affectation_type": "10"
    },
    {
      "product_code": "PROD-001",
      "description": "Producto demo",
      "unit_code": "NIU",
      "quantity": 1,
      "unit_price": 50,
      "tax_affectation_type": "10"
    }
  ]
}
```

The API calculates invoice totals from the item lines. Clients do not send `taxable_amount`, `igv_amount` or `total_amount`.

Show invoice:

```http
GET /api/invoices/{id}
```

## Invoice Header Fields

| Field | Purpose |
| --- | --- |
| `document_type` | Electronic document type. `01` invoice, `03` receipt, `07` credit note, `08` debit note |
| `operation_type` | SUNAT/Greenter operation type. Default `0101` |
| `series` / `number` | Document numbering |
| `issue_date` / `due_date` | Document dates |
| `customer_document_type` | Customer document type. Example: `6` for RUC |
| `customer_document_number` | Customer document number |
| `taxable_amount` | Calculated taxable base |
| `igv_amount` | Calculated IGV amount |
| `total_amount` | Calculated document total |

## Invoice Statuses

| Status | Meaning |
| --- | --- |
| `pending` | Invoice was created and is waiting for async processing |
| `processing` | Worker is processing the invoice |
| `processed` | Worker finished and stored the generated file |
| `failed` | Worker failed to process the invoice |

## Architecture Direction

Current repositories:

| Repository | Role |
| --- | --- |
| `laravel-floci-s3-lab` | Storage API / S3 lab |
| `laravel-aws-sqs-lab` | SQS learning lab |
| `laravel-invoice-api` | Business API for invoices |

Next repository planned:

| Repository | Role |
| --- | --- |
| `laravel-invoice-worker` | Background worker that consumes SQS and calls the storage API |

## Current Status

- Laravel 12 base project is pushed.
- Docker services are configured.
- MySQL environment variables are prepared.
- Invoice header, items, service, controller and API routes are ready.
- SQS publishing is pending.
