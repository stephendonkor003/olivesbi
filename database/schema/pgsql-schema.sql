-- PostgreSQL schema converted from database/schema/mysql-schema.sql
-- Generated for the olivesbi pgsql connection. Review before production use.

SET client_encoding = 'UTF8';
SET check_function_bodies = false;

DROP TABLE IF EXISTS "agent_assignments" CASCADE;
CREATE TABLE "agent_assignments" (
  "id" bigserial NOT NULL,
  "supervisor_id" varchar(255) NOT NULL,
  "agent_id" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "agent_live_migrate" CASCADE;
CREATE TABLE "agent_live_migrate" (
  "gid" integer DEFAULT NULL,
  "agent_name" varchar(254) DEFAULT NULL,
  "datetime" timestamp(0) without time zone DEFAULT NULL,
  "the_geom" text,
  "license_code" varchar(255) DEFAULT NULL
);

DROP TABLE IF EXISTS "assemblies" CASCADE;
CREATE TABLE "assemblies" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "regional_code" varchar(255) NOT NULL,
  "address" varchar(255) DEFAULT NULL,
  "phone" varchar(255) DEFAULT NULL,
  "geo_coordinate" jsonb DEFAULT NULL,
  "status" varchar(255) DEFAULT NULL,
  "supervisor" varchar(255) DEFAULT NULL,
  "logo" varchar(255) DEFAULT NULL,
  "invoice_layout" varchar(255) DEFAULT NULL,
  "assembly_category" varchar(255) DEFAULT NULL,
  "bank_name" varchar(255) DEFAULT NULL,
  "bank_number" varchar(255) DEFAULT NULL,
  "bank_branch" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "audit_trails" CASCADE;
CREATE TABLE "audit_trails" (
  "id" bigserial NOT NULL,
  "user_id" varchar(255) NOT NULL,
  "action_performed" varchar(255) NOT NULL,
  "action_date" varchar(255) NOT NULL,
  "ip_address" varchar(255) NOT NULL,
  "device_used" varchar(255) NOT NULL,
  "remarks" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "bill_adjustments" CASCADE;
CREATE TABLE "bill_adjustments" (
  "id" bigserial NOT NULL,
  "revenue_stream_type_id" varchar(255) NOT NULL,
  "discount_year" varchar(255) NOT NULL,
  "discount_amount" varchar(255) NOT NULL,
  "discount_type" varchar(255) DEFAULT NULL,
  "assembly_code" varchar(255) DEFAULT NULL,
  "division_id" varchar(255) DEFAULT NULL,
  "block_id" varchar(255) DEFAULT NULL,
  "erms_id" json DEFAULT NULL,
  "remarks" text,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "bills" CASCADE;
CREATE TABLE "bills" (
  "id" bigserial NOT NULL,
  "property_id" varchar(255) DEFAULT NULL,
  "business_id" varchar(255) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "bills_id" varchar(255) NOT NULL,
  "bills_year" varchar(255) NOT NULL,
  "arrears" varchar(255) NOT NULL,
  "amount" decimal(10,2) NOT NULL,
  "discount_amount" varchar(255) DEFAULT NULL,
  "billing_date" date NOT NULL,
  "issue_bill" varchar(255) NOT NULL DEFAULT 'No',
  "issued_by" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "blocks" CASCADE;
CREATE TABLE "blocks" (
  "id" bigserial NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "name" varchar(255) NOT NULL,
  "code" varchar(255) NOT NULL,
  "geofence" jsonb DEFAULT NULL,
  "division_id" bigint NOT NULL DEFAULT 0,
  "created_on" varchar(255) DEFAULT NULL,
  "updated_on" varchar(255) DEFAULT NULL,
  "deleted_on" varchar(255) DEFAULT NULL,
  "created_by" char(36) DEFAULT NULL,
  "deleted_by" char(36) DEFAULT NULL,
  "updated_by" char(36) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "old_id" bigint DEFAULT NULL,
  "old_division_id" bigint DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "bop_rates" CASCADE;
CREATE TABLE "bop_rates" (
  "id" bigserial NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "zone_id" varchar(255) NOT NULL,
  "property_use_id" varchar(255) NOT NULL,
  "amount" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "buildings" CASCADE;
CREATE TABLE "buildings" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "block_id" varchar(255) NOT NULL,
  "boundary" text NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "business_class_types" CASCADE;
CREATE TABLE "business_class_types" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "category" varchar(255) NOT NULL,
  "rate" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "identifier" varchar(255) DEFAULT NULL,
  "extra_class_identifier" varchar(255) DEFAULT NULL,
  "classcode" varchar(255) DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "business_classes" CASCADE;
CREATE TABLE "business_classes" (
  "id" bigserial NOT NULL,
  "business_type_id" varchar(255) NOT NULL,
  "name" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "business_owners" CASCADE;
CREATE TABLE "business_owners" (
  "id" bigserial NOT NULL,
  "erms_id" varchar(255) NOT NULL,
  "firstname" varchar(255) DEFAULT NULL,
  "lastname" varchar(255) DEFAULT NULL,
  "middlename" varchar(255) DEFAULT NULL,
  "gender" varchar(255) DEFAULT NULL,
  "tin_number" varchar(255) DEFAULT NULL,
  "organization_name" varchar(255) DEFAULT NULL,
  "email" varchar(255) DEFAULT NULL,
  "primary_phone" varchar(255) DEFAULT NULL,
  "secondary_phone" varchar(255) DEFAULT NULL,
  "house_number" varchar(255) DEFAULT NULL,
  "digital_address" varchar(255) DEFAULT NULL,
  "residential_address" varchar(255) DEFAULT NULL,
  "postal_address" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "business_types" CASCADE;
CREATE TABLE "business_types" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "parent_category" varchar(255) NOT NULL,
  "sub_categories" jsonb DEFAULT NULL,
  "rate" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "businesses" CASCADE;
CREATE TABLE "businesses" (
  "id" bigserial NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "account_no" varchar(255) NOT NULL,
  "business_name" varchar(255) NOT NULL,
  "registration_no" varchar(255) DEFAULT NULL,
  "permit_no" varchar(255) DEFAULT NULL,
  "buss_rev_id" bigint DEFAULT NULL,
  "is_bsc_rate" jsonb DEFAULT NULL,
  "hseno" varchar(255) DEFAULT NULL,
  "digitaladdress" varchar(255) DEFAULT NULL,
  "polygon_id" bigint DEFAULT NULL,
  "quantity" integer NOT NULL DEFAULT 0,
  "location" varchar(255) DEFAULT NULL,
  "street_name" varchar(255) DEFAULT NULL,
  "phone_no" varchar(255) DEFAULT NULL,
  "address" text,
  "email" varchar(255) DEFAULT NULL,
  "frntpicture_path" varchar(255) DEFAULT NULL,
  "othrpicture_path" varchar(255) DEFAULT NULL,
  "validation_id" varchar(255) DEFAULT NULL,
  "owner_id" varchar(100) DEFAULT NULL,
  "generate_inv" boolean NOT NULL DEFAULT false,
  "remarks" text,
  "created_on" varchar(255) DEFAULT NULL,
  "updated_on" varchar(255) DEFAULT NULL,
  "deleted_on" varchar(255) DEFAULT NULL,
  "created_by" char(36) DEFAULT NULL,
  "deleted_by" char(36) DEFAULT NULL,
  "updated_by" char(36) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "old_id" bigint DEFAULT NULL,
  "old_buss_rev_id" bigint DEFAULT NULL,
  "old_polygon_id" bigint DEFAULT NULL,
  "old_validation_id" varchar(255) DEFAULT NULL,
  "old_owner_id" varchar(255) DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "cache" CASCADE;
CREATE TABLE "cache" (
  "key" varchar(255) NOT NULL,
  "value" text NOT NULL,
  "expiration" integer NOT NULL,
  PRIMARY KEY ("key")
);

DROP TABLE IF EXISTS "call_back_requests" CASCADE;
CREATE TABLE "call_back_requests" (
  "id" bigserial NOT NULL,
  "request_id" varchar(255) NOT NULL,
  "name" varchar(255) NOT NULL,
  "email" varchar(255) NOT NULL,
  "phone" varchar(255) NOT NULL,
  "phone_alt" varchar(255) DEFAULT NULL,
  "message" text NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "categories" CASCADE;
CREATE TABLE "categories" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "citizens" CASCADE;
CREATE TABLE "citizens" (
  "id" bigserial NOT NULL,
  "user_id" varchar(255) NOT NULL,
  "first_name" varchar(255) DEFAULT NULL,
  "last_name" varchar(255) DEFAULT NULL,
  "prefix" varchar(255) DEFAULT NULL,
  "gender" varchar(255) DEFAULT NULL,
  "date_of_birth" varchar(255) DEFAULT NULL,
  "marital_status" varchar(255) DEFAULT NULL,
  "account_number" varchar(255) DEFAULT NULL,
  "telephone_number" varchar(255) DEFAULT NULL,
  "country_of_citizenship" varchar(255) DEFAULT NULL,
  "customer_type" varchar(255) DEFAULT NULL,
  "status" varchar(255) NOT NULL,
  "location" varchar(255) DEFAULT NULL,
  "vlst_no" varchar(255) DEFAULT NULL,
  "Ghana_card_number" varchar(255) DEFAULT NULL,
  "id_type" varchar(255) DEFAULT NULL,
  "id_number" varchar(255) DEFAULT NULL,
  "business_name" varchar(255) DEFAULT NULL,
  "email" varchar(255) DEFAULT NULL,
  "date_of_commencement" varchar(255) DEFAULT NULL,
  "security_question" text,
  "security_answer" text,
  "tin_number" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "customer_supports" CASCADE;
CREATE TABLE "customer_supports" (
  "id" bigserial NOT NULL,
  "user_id" varchar(255) NOT NULL,
  "description" text NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "status" varchar(255) NOT NULL DEFAULT 'Pending',
  "response" varchar(255) DEFAULT NULL,
  "response_by" varchar(255) DEFAULT NULL,
  "response_date" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "customer_types" CASCADE;
CREATE TABLE "customer_types" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "status" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "divisions" CASCADE;
CREATE TABLE "divisions" (
  "id" bigserial NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "name" varchar(255) NOT NULL,
  "code" varchar(255) NOT NULL,
  "geofence" jsonb DEFAULT NULL,
  "created_on" varchar(255) DEFAULT NULL,
  "updated_on" varchar(255) DEFAULT NULL,
  "deleted_on" varchar(255) DEFAULT NULL,
  "created_by" char(36) DEFAULT NULL,
  "deleted_by" char(36) DEFAULT NULL,
  "updated_by" char(36) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "old_id" bigint DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "entity_owners" CASCADE;
CREATE TABLE "entity_owners" (
  "id" bigserial NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "entity_type_id" varchar(255) DEFAULT NULL,
  "organization_name" varchar(255) DEFAULT NULL,
  "fname" varchar(255) DEFAULT NULL,
  "lname" varchar(255) DEFAULT NULL,
  "mname" varchar(255) DEFAULT NULL,
  "email" varchar(255) DEFAULT NULL,
  "gender" varchar(255) DEFAULT NULL,
  "phone_no_primary" varchar(255) NOT NULL,
  "phone_no_secondary" varchar(255) DEFAULT NULL,
  "tin_no" varchar(255) DEFAULT NULL,
  "status" varchar(255) NOT NULL DEFAULT 'Active',
  "house_no" varchar(255) DEFAULT NULL,
  "residential_address" varchar(255) DEFAULT NULL,
  "postal_address" varchar(255) DEFAULT NULL,
  "digital_address" varchar(255) DEFAULT NULL,
  "location_geopoint" jsonb DEFAULT NULL,
  "created_on" varchar(255) DEFAULT NULL,
  "updated_on" varchar(255) DEFAULT NULL,
  "deleted_on" varchar(255) DEFAULT NULL,
  "created_by" char(36) DEFAULT NULL,
  "deleted_by" char(36) DEFAULT NULL,
  "updated_by" char(36) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "old_id" bigint DEFAULT NULL,
  "old_entity_type_id" varchar(255) DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "failed_jobs" CASCADE;
CREATE TABLE "failed_jobs" (
  "id" bigserial NOT NULL,
  "uuid" varchar(255) NOT NULL,
  "connection" text NOT NULL,
  "queue" text NOT NULL,
  "payload" text NOT NULL,
  "exception" text NOT NULL,
  "failed_at" timestamp(0) without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "job_batches" CASCADE;
CREATE TABLE "job_batches" (
  "id" varchar(255) NOT NULL,
  "name" varchar(255) NOT NULL,
  "total_jobs" integer NOT NULL,
  "pending_jobs" integer NOT NULL,
  "failed_jobs" integer NOT NULL,
  "failed_job_ids" text NOT NULL,
  "options" text DEFAULT NULL,
  "cancelled_at" integer DEFAULT NULL,
  "created_at" integer NOT NULL,
  "finished_at" integer DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "ghana_regions" CASCADE;
CREATE TABLE "ghana_regions" (
  "id" bigserial NOT NULL,
  "regional_code" varchar(255) NOT NULL,
  "name" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "invoice_layout_templates" CASCADE;
CREATE TABLE "invoice_layout_templates" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "layout" text NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "invoices" CASCADE;
CREATE TABLE "invoices" (
  "id" bigserial NOT NULL,
  "min_amt_2" decimal(10,2) NOT NULL,
  "bill_serial" varchar(255) NOT NULL,
  "bill_no" varchar(255) NOT NULL,
  "bill_date" date NOT NULL,
  "due_date" date NOT NULL,
  "arrears" decimal(10,2) NOT NULL,
  "current_amount" decimal(10,2) NOT NULL,
  "amount_due" decimal(10,2) NOT NULL,
  "account_no" varchar(255) NOT NULL,
  "status" varchar(255) DEFAULT NULL,
  "amount_paid" decimal(10,2) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "item_descriptions" CASCADE;
CREATE TABLE "item_descriptions" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "main_items" CASCADE;
CREATE TABLE "main_items" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "migrations" CASCADE;
CREATE TABLE "migrations" (
  "id" serial NOT NULL,
  "migration" varchar(255) NOT NULL,
  "batch" integer NOT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "mmdas" CASCADE;
CREATE TABLE "mmdas" (
  "id" bigserial NOT NULL,
  "region_id" varchar(255) NOT NULL,
  "region_code" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "assembly_name" varchar(255) NOT NULL,
  "assembly_id" varchar(255) NOT NULL,
  "assembly_category" varchar(255) NOT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "model_has_permissions" CASCADE;
CREATE TABLE "model_has_permissions" (
  "permission_id" bigint NOT NULL,
  "model_type" varchar(255) NOT NULL,
  "model_id" bigint NOT NULL,
  PRIMARY KEY ("permission_id","model_id","model_type")
);

DROP TABLE IF EXISTS "model_has_roles" CASCADE;
CREATE TABLE "model_has_roles" (
  "role_id" bigint NOT NULL,
  "model_type" varchar(255) NOT NULL,
  "model_id" bigint NOT NULL,
  PRIMARY KEY ("role_id","model_id","model_type")
);

DROP TABLE IF EXISTS "otps" CASCADE;
CREATE TABLE "otps" (
  "id" bigserial NOT NULL,
  "citizen_id" varchar(255) DEFAULT NULL,
  "bills_id" varchar(255) DEFAULT NULL,
  "code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "type" varchar(255) DEFAULT NULL,
  "user_id" bigint DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "password_reset_tokens" CASCADE;
CREATE TABLE "password_reset_tokens" (
  "email" varchar(255) NOT NULL,
  "token" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("email")
);

DROP TABLE IF EXISTS "payments" CASCADE;
CREATE TABLE "payments" (
  "id" bigserial NOT NULL,
  "bills_id" varchar(255) NOT NULL,
  "payment_mode" varchar(255) DEFAULT NULL,
  "payment_source" varchar(255) DEFAULT NULL,
  "transaction_status" varchar(255) DEFAULT NULL,
  "transaction_id" varchar(255) DEFAULT NULL,
  "authorization_url" varchar(255) DEFAULT NULL,
  "req_transaction_id" varchar(255) DEFAULT NULL,
  "amount" varchar(255) DEFAULT NULL,
  "phone" varchar(255) DEFAULT NULL,
  "network" varchar(255) DEFAULT NULL,
  "prompt" text,
  "assembly_code" varchar(255) NOT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "receipt" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "permissions" CASCADE;
CREATE TABLE "permissions" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "guard_name" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "personal_access_tokens" CASCADE;
CREATE TABLE "personal_access_tokens" (
  "id" bigserial NOT NULL,
  "tokenable_type" varchar(255) NOT NULL,
  "tokenable_id" bigint NOT NULL,
  "name" varchar(255) NOT NULL,
  "token" varchar(64) NOT NULL,
  "abilities" text,
  "last_used_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "expires_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "polygon_blocks" CASCADE;
CREATE TABLE "polygon_blocks" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "block_number" varchar(255) DEFAULT NULL,
  "boundary" text NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "polygons" CASCADE;
CREATE TABLE "polygons" (
  "id" bigserial NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "building" jsonb DEFAULT NULL,
  "block_id" bigint DEFAULT NULL,
  "building_id" bigint DEFAULT NULL,
  "created_on" varchar(255) DEFAULT NULL,
  "updated_on" varchar(255) DEFAULT NULL,
  "deleted_on" varchar(255) DEFAULT NULL,
  "created_by" char(36) DEFAULT NULL,
  "deleted_by" char(36) DEFAULT NULL,
  "updated_by" char(36) DEFAULT NULL,
  "cntr_geom" varchar(255) DEFAULT NULL,
  "cntr_x_coord" decimal(10,8) DEFAULT NULL,
  "cntr_y_coord" decimal(10,8) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "old_id" bigint DEFAULT NULL,
  "old_block_id" bigint DEFAULT NULL,
  "old_building_id" bigint DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "properties" CASCADE;
CREATE TABLE "properties" (
  "id" bigserial NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "account_no" varchar(255) DEFAULT NULL,
  "polygon_id" varchar(255) DEFAULT NULL,
  "hseno" varchar(255) DEFAULT NULL,
  "digitaladdress" varchar(255) DEFAULT NULL,
  "location" varchar(255) DEFAULT NULL,
  "streetname" varchar(255) DEFAULT NULL,
  "prop_rev_id" varchar(255) DEFAULT NULL,
  "prop_zone_id" varchar(255) DEFAULT NULL,
  "is_bsc_rate" jsonb DEFAULT NULL,
  "owner_id" varchar(255) DEFAULT NULL,
  "is_rated" boolean DEFAULT NULL,
  "vlst_no" varchar(255) DEFAULT NULL,
  "rateable_value" decimal(12,2) DEFAULT NULL,
  "frntpicture_path" varchar(255) DEFAULT NULL,
  "othrpicture_path" varchar(255) DEFAULT NULL,
  "validation_id" varchar(255) DEFAULT NULL,
  "generate_inv" boolean DEFAULT NULL,
  "remarks" varchar(255) DEFAULT NULL,
  "created_on" varchar(255) DEFAULT NULL,
  "updated_on" varchar(255) DEFAULT NULL,
  "deleted_on" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "deleted_by" varchar(255) DEFAULT NULL,
  "updated_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "assembly_code" varchar(50) DEFAULT NULL,
  "old_id" bigint DEFAULT NULL,
  "old_polygon_id" bigint DEFAULT NULL,
  "old_prop_rev_id" bigint DEFAULT NULL,
  "old_prop_zone_id" bigint DEFAULT NULL,
  "old_owner_id" varchar(255) DEFAULT NULL,
  "old_validation_id" varchar(255) DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "property_class_revenues" CASCADE;
CREATE TABLE "property_class_revenues" (
  "id" bigserial NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "name" varchar(255) NOT NULL,
  "code" varchar(255) NOT NULL,
  "descn" text,
  "parent_id" bigint NOT NULL DEFAULT 0,
  "rate_impost" decimal(10,2) NOT NULL DEFAULT 0.00,
  "flat_rate" decimal(10,2) NOT NULL DEFAULT 0.00,
  "min_amount" decimal(10,2) DEFAULT NULL,
  "max_amount" decimal(10,2) DEFAULT NULL,
  "is_rated" boolean NOT NULL DEFAULT false,
  "is_applicable" boolean NOT NULL DEFAULT true,
  "frequency" jsonb DEFAULT NULL,
  "start_generation" date DEFAULT NULL,
  "last_generated_date" date DEFAULT NULL,
  "template_id" bigint DEFAULT NULL,
  "created_on" varchar(255) DEFAULT NULL,
  "updated_on" varchar(255) DEFAULT NULL,
  "deleted_on" varchar(255) DEFAULT NULL,
  "created_by" char(36) DEFAULT NULL,
  "deleted_by" char(36) DEFAULT NULL,
  "updated_by" char(36) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "property_classes" CASCADE;
CREATE TABLE "property_classes" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "property_types" CASCADE;
CREATE TABLE "property_types" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "property_users" CASCADE;
CREATE TABLE "property_users" (
  "id" bigserial NOT NULL,
  "zone_id" varchar(255) NOT NULL,
  "name" varchar(255) NOT NULL,
  "assembly_code" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "rates" CASCADE;
CREATE TABLE "rates" (
  "id" bigserial NOT NULL,
  "zone_id" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "property_use_id" varchar(255) NOT NULL,
  "rate" varchar(255) NOT NULL,
  "minimum_rate" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "report_uploads" CASCADE;
CREATE TABLE "report_uploads" (
  "id" bigserial NOT NULL,
  "task_id" varchar(255) NOT NULL,
  "description" text NOT NULL,
  "file_path" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "revenue_stream_types" CASCADE;
CREATE TABLE "revenue_stream_types" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "revenue_streams" CASCADE;
CREATE TABLE "revenue_streams" (
  "id" bigserial NOT NULL,
  "revenue_stream_type_id" varchar(255) NOT NULL,
  "code" varchar(255) DEFAULT NULL,
  "main_item_id" varchar(255) DEFAULT NULL,
  "sub_item_id" varchar(255) DEFAULT NULL,
  "description" varchar(255) DEFAULT NULL,
  "zone_type_id" varchar(255) DEFAULT NULL,
  "property_classes_id" varchar(255) DEFAULT NULL,
  "property_type_id" varchar(255) DEFAULT NULL,
  "category_id" varchar(255) DEFAULT NULL,
  "description_id" varchar(255) DEFAULT NULL,
  "rate_impost" varchar(255) DEFAULT NULL,
  "flat_rate" varchar(255) DEFAULT NULL,
  "min_amount" varchar(255) DEFAULT NULL,
  "max_amount" varchar(255) DEFAULT NULL,
  "is_rated" varchar(255) DEFAULT 'Y',
  "duration" varchar(255) DEFAULT NULL,
  "template_id" varchar(255) DEFAULT NULL,
  "basic_rate" varchar(255) DEFAULT NULL,
  "assembly_code" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "role_has_permissions" CASCADE;
CREATE TABLE "role_has_permissions" (
  "permission_id" bigint NOT NULL,
  "role_id" bigint NOT NULL,
  PRIMARY KEY ("permission_id","role_id")
);

DROP TABLE IF EXISTS "role_user" CASCADE;
CREATE TABLE "role_user" (
  "id" bigserial NOT NULL,
  "role_id" bigint NOT NULL,
  "user_id" bigint NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "roles" CASCADE;
CREATE TABLE "roles" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "guard_name" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "active" boolean NOT NULL DEFAULT true,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "service_requests" CASCADE;
CREATE TABLE "service_requests" (
  "id" bigserial NOT NULL,
  "user_id" varchar(255) NOT NULL,
  "service_used" varchar(255) NOT NULL,
  "usage_date" varchar(255) NOT NULL,
  "service_channel" varchar(255) NOT NULL,
  "status" varchar(255) NOT NULL DEFAULT 'Pending',
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "sessions" CASCADE;
CREATE TABLE "sessions" (
  "id" varchar(255) NOT NULL,
  "user_id" bigint DEFAULT NULL,
  "ip_address" varchar(45) DEFAULT NULL,
  "user_agent" text,
  "payload" text NOT NULL,
  "last_activity" integer NOT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "sub_items" CASCADE;
CREATE TABLE "sub_items" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "task_assignments" CASCADE;
CREATE TABLE "task_assignments" (
  "id" bigserial NOT NULL,
  "supervisor_id" varchar(255) NOT NULL,
  "agent_id" varchar(255) NOT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "task" varchar(255) NOT NULL,
  "block_data" jsonb NOT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "users" CASCADE;
CREATE TABLE "users" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "email" varchar(255) NOT NULL,
  "phone" varchar(255) NOT NULL,
  "password" varchar(255) NOT NULL,
  "logging_type" varchar(255) DEFAULT NULL,
  "email_verified_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "remember_token" varchar(100) DEFAULT NULL,
  "status" varchar(255) NOT NULL DEFAULT 'active',
  "gender" varchar(255) DEFAULT NULL,
  "access_level" varchar(255) DEFAULT NULL,
  "regional_code" varchar(255) DEFAULT NULL,
  "assembly_code" varchar(255) DEFAULT NULL,
  "division_code" varchar(255) DEFAULT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) DEFAULT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "role_access" varchar(255) DEFAULT NULL,
  "change_password" boolean NOT NULL DEFAULT false,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "validated_businesses" CASCADE;
CREATE TABLE "validated_businesses" (
  "id" char(36) NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "business_id" bigint NOT NULL,
  "validation_list_id" varchar(255) NOT NULL,
  "validated_by" char(36) NOT NULL,
  "validated_on" varchar(255) DEFAULT NULL,
  "validation_msg" text,
  "created_on" varchar(255) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "validated_properties" CASCADE;
CREATE TABLE "validated_properties" (
  "id" char(36) NOT NULL,
  "erms_id" varchar(255) DEFAULT NULL,
  "property_id" bigint NOT NULL,
  "validation_list_id" varchar(255) NOT NULL,
  "validated_by" char(36) NOT NULL,
  "validated_on" varchar(255) DEFAULT NULL,
  "validation_msg" text,
  "created_on" varchar(255) DEFAULT NULL,
  "assembly_code" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "valuation_lists" CASCADE;
CREATE TABLE "valuation_lists" (
  "Region" varchar(200) DEFAULT NULL,
  "Assembly_Code" varchar(200) DEFAULT NULL,
  "Assembly_Name" varchar(200) DEFAULT NULL,
  "Valuation_Number" varchar(200) DEFAULT NULL,
  "Longitude" varchar(200) DEFAULT NULL,
  "Latitude" varchar(200) DEFAULT NULL,
  "Digital_Address" varchar(200) DEFAULT NULL,
  "House_Number" varchar(200) DEFAULT NULL,
  "Suburb_P" varchar(200) DEFAULT NULL,
  "Valuation_Zone_Code" varchar(200) DEFAULT NULL,
  "pu_code" varchar(200) DEFAULT NULL,
  "Occupier" varchar(200) DEFAULT NULL,
  "Owner_Name" varchar(200) DEFAULT NULL,
  "Owner_Email" varchar(200) DEFAULT NULL,
  "Owner_Phone_Number" varchar(200) DEFAULT NULL,
  "Rateable_Value" varchar(200) DEFAULT NULL,
  "AssemblyID" varchar(200) DEFAULT NULL,
  "Centroid" varchar(200) DEFAULT NULL
);

DROP TABLE IF EXISTS "zones" CASCADE;
CREATE TABLE "zones" (
  "id" bigserial NOT NULL,
  "name" varchar(255) NOT NULL,
  "assembly_code" varchar(255) DEFAULT NULL,
  "created_by" varchar(255) NOT NULL,
  "created_at" timestamp(0) without time zone NULL DEFAULT NULL,
  "updated_at" timestamp(0) without time zone NULL DEFAULT NULL,
  PRIMARY KEY ("id")
);

-- Indexes
CREATE UNIQUE INDEX "call_back_requests_request_id_unique" ON "call_back_requests" ("request_id");
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" ON "failed_jobs" ("uuid");
CREATE UNIQUE INDEX "ghana_regions_regional_code_unique" ON "ghana_regions" ("regional_code");
CREATE INDEX "model_has_permissions_model_id_model_type_index" ON "model_has_permissions" ("model_id","model_type");
CREATE INDEX "model_has_roles_model_id_model_type_index" ON "model_has_roles" ("model_id","model_type");
CREATE INDEX "otps_user_id_index" ON "otps" ("user_id");
CREATE UNIQUE INDEX "permissions_name_guard_name_unique" ON "permissions" ("name","guard_name");
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" ON "personal_access_tokens" ("token");
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" ON "personal_access_tokens" ("tokenable_type","tokenable_id");
CREATE INDEX "property_classes_assembly_code_index" ON "property_classes" ("assembly_code");
CREATE INDEX "property_types_assembly_code_index" ON "property_types" ("assembly_code");
CREATE INDEX "revenue_stream_types_assembly_code_index" ON "revenue_stream_types" ("assembly_code");
CREATE INDEX "role_has_permissions_role_id_foreign" ON "role_has_permissions" ("role_id");
CREATE INDEX "role_user_role_id_foreign" ON "role_user" ("role_id");
CREATE INDEX "role_user_user_id_foreign" ON "role_user" ("user_id");
CREATE UNIQUE INDEX "roles_name_guard_name_unique" ON "roles" ("name","guard_name");
CREATE INDEX "sessions_user_id_index" ON "sessions" ("user_id");
CREATE INDEX "sessions_last_activity_index" ON "sessions" ("last_activity");
CREATE UNIQUE INDEX "users_email_unique" ON "users" ("email");
CREATE UNIQUE INDEX "users_phone_unique" ON "users" ("phone");

-- Foreign keys
ALTER TABLE "model_has_permissions" ADD CONSTRAINT "model_has_permissions_permission_id_foreign" FOREIGN KEY ("permission_id") REFERENCES "permissions" ("id") ON DELETE CASCADE;
ALTER TABLE "model_has_roles" ADD CONSTRAINT "model_has_roles_role_id_foreign" FOREIGN KEY ("role_id") REFERENCES "roles" ("id") ON DELETE CASCADE;
ALTER TABLE "role_has_permissions" ADD CONSTRAINT "role_has_permissions_permission_id_foreign" FOREIGN KEY ("permission_id") REFERENCES "permissions" ("id") ON DELETE CASCADE;
ALTER TABLE "role_has_permissions" ADD CONSTRAINT "role_has_permissions_role_id_foreign" FOREIGN KEY ("role_id") REFERENCES "roles" ("id") ON DELETE CASCADE;
ALTER TABLE "role_user" ADD CONSTRAINT "role_user_role_id_foreign" FOREIGN KEY ("role_id") REFERENCES "roles" ("id") ON DELETE CASCADE;
ALTER TABLE "role_user" ADD CONSTRAINT "role_user_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;

-- Existing migration history from the schema dump
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (100,'2014_10_12_000000_create_users_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (101,'2014_10_12_100000_create_password_reset_tokens_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (102,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (103,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (104,'2023_10_06_084440_create_permission_tables',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (106,'2024_04_26_135058_create_citizens_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (107,'2024_04_26_135628_create_businesses_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (108,'2024_04_26_140532_create_bills_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (109,'2024_04_26_141141_create_payments_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (110,'2024_04_27_200823_create_business_class_types_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (111,'2024_04_28_183003_add_identifier_to_business_class_types_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (112,'2024_04_28_203433_add_extra_class_identifier_to_business_class_types_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (113,'2024_04_28_204203_add_classcode_to_business_class_types_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (114,'2024_05_05_052829_create_properties_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (115,'2024_05_07_141522_add_customer_name_to_properties_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (116,'2024_05_08_153153_add_property_id_to_bills_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (117,'2024_05_10_185133_add_columns_to_citizens_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (118,'2024_05_12_115820_create_invoices_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (119,'2024_05_12_130635_add_status_to_invoices_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (120,'2024_05_12_143541_add_amount_paid_to_invoices_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (121,'2024_05_12_153935_add_location_and_vlst_no_to_citizens_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (122,'2024_06_11_161539_add_logging_type_to_users_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (123,'2024_06_11_162406_create_ghana_regions_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (124,'2024_06_11_162833_create_assemblies_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (125,'2024_06_11_214031_add_assembly_code_to_properties_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (126,'2024_06_11_215438_add_longitude_and_latitude_to_properties_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (127,'2024_06_12_084158_create_divisions_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (128,'2024_06_12_084517_create_blocks_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (129,'2024_06_12_224901_add_extra_fields_to_users_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (130,'2024_06_13_121207_add_columns_to_assemblies_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (131,'2024_06_13_123641_create_invoice_layout_templates_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (132,'2024_06_17_074917_add_role_access_to_users_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (133,'2024_06_17_083848_create_role_user_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (134,'2024_06_22_152345_create_ablekuma_north_payment_temp_table',1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (135,'2024_07_05_122237_add_columns_to_ablekuma_north_payment_temp_table',2);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (136,'2024_07_06_211700_make_columns_nullable_in_ablekuma_north_payment_temp_table',3);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (137,'2024_07_08_135206_add_column_to_ablekuma_north_payment_temp_table',4);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (138,'2024_07_08_142109_make_columns_nullable_in_payments_table',5);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (141,'2024_07_08_143857_create_ablekuma_north_invoice_payments_table',6);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (142,'2024_07_30_141434_make_column_varchar_in_divisions_table',7);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (143,'2024_07_30_155432_add_column_to_blocks_table',8);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (145,'2024_04_26_134842_create_customer_types_table',9);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (146,'2024_08_01_095115_create_business_types_table',10);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (147,'2024_08_01_095136_create_business_classes_table',10);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (148,'2024_08_01_132456_add_columns_to_businesses_table',11);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (149,'2024_08_01_133109_create_business_owners_table',11);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (150,'2024_10_07_144450_create_zones_table',12);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (151,'2024_10_07_144538_create_property_users_table',12);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (154,'2024_10_07_194612_add_column_to_property_users_table',13);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (155,'2024_10_07_144631_create_reteable_values_table',14);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (156,'2024_10_07_205920_drop_column_under_property_users_tables',15);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (157,'2024_10_08_085108_add_columns_to_properties_table',16);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (158,'2024_10_08_085647_modify_columns_position_in_table',17);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (159,'2024_10_08_092251_change_column_name_in_rates_table',18);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (160,'2024_10_08_105332_add_column_to_bills_table',19);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (161,'2024_10_08_110222_change_column_position_in_bills_table',20);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (162,'2024_10_09_151043_add_columns_to_properties_table',21);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (163,'2024_10_09_194851_change_column_name_under_payments_table',22);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (164,'2024_10_10_074644_add_column_to_bills_table',23);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (165,'2024_10_10_093151_add_columns_to_businesses_table',24);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (166,'2024_10_07_144715_create_bop_bills_table',25);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (167,'2024_10_11_075844_add_column_to_payments_table',26);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (168,'2024_10_11_103933_add_column_to_payments_table',27);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (169,'2024_10_12_102717_add_column_to_citizens_table',28);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (170,'2024_10_17_092704_add_column_to_users_table',29);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (171,'2024_10_22_180449_remove_column_from_citizens_table',30);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (172,'2024_10_22_214519_add_column_to_citizens_table',31);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (173,'2024_10_24_085023_remove_column_from_citizens_table',32);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (174,'2024_10_24_093919_create_otps_table',33);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (175,'2024_10_25_075536_create_agent_assignments_table',34);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (178,'2024_10_25_101006_create_task_assignments_table',35);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (179,'2024_11_08_103611_create_report_uploads_table',36);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (180,'2024_11_08_141219_create_customer_supports_table',37);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (181,'2024_12_10_123350_add_columns_to_assemblies_table',38);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (182,'2024_12_17_174032_change_column_type_in_assemblies_table',39);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (183,'2024_12_17_175215_add_column_to_assemblies_table',40);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (184,'2024_12_17_180056_change_position_of_a_column_in_assemblies_table',41);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (185,'2024_12_17_180411_change_column_position_in_assemblies_table',42);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (186,'2024_12_17_184825_make_column_nullable_in_assemblies_table',43);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (188,'2025_01_03_055721_add_column_to_bills_table',44);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (191,'2025_01_08_084908_add_column_to_users_table',45);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (192,'2025_01_19_134718_create_service_requests_table',46);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (193,'2025_01_19_172024_create_audit_trails_table',47);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (194,'2025_01_24_080124_make_column_nullable_under_citizens_table',48);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (195,'2025_02_15_172151_make_column_nullable_from_properties_table',49);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (196,'2025_02_16_114506_add_column_to_payments_table',50);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (197,'2025_02_17_095919_make_column_nullable_from_payments_table',51);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (199,'2025_03_14_115819_create_buildings_table',52);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (200,'2025_03_15_054445_create_polygon_blocks_table',53);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (201,'2025_03_24_142036_add_column_to_polygon_blocks_table',54);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (202,'2025_04_08_082229_create_mmdas_table',55);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (203,'2025_04_12_113709_add_column_to_users_table',56);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (204,'2025_04_29_104931_add_column_to_assemblies_table',57);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (205,'2025_04_29_141343_add_column_to_zones_table',58);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (206,'2025_06_28_081954_add_column_to_bills_table',59);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (207,'2025_07_11_082119_add_column_to_users_table',60);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (208,'2025_07_16_170854_create_revenue_stream_types_table',61);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (209,'2025_07_16_170950_create_revenue_streams_table',61);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (210,'2025_07_16_191930_create_property_classes_table',62);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (211,'2025_07_17_073653_create_property_types_table',63);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (212,'2025_07_17_081124_add_column_to_revenue_streams_table',64);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (213,'2025_07_17_115705_create_categories_table',65);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (214,'2025_07_17_130833_create_item_descriptions_table',66);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (215,'2025_07_17_131917_add_column_to_revenue_streams_table',67);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (216,'2025_07_17_145841_add_column_to_revenue_streams_table',68);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (217,'2025_07_18_114009_add_column_to_payments_table',69);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (218,'2025_07_18_151327_add_column_to_entity_owners_table',70);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (219,'2025_08_24_100751_create_bill_adjustments_table',71);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (220,'2025_08_25_113433_add_column_to_bill_adjustments_table',71);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (221,'2025_08_26_153903_add_column_to_bills_table',71);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (222,'2025_09_01_183406_create_main_items_table',72);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (223,'2025_09_01_183425_create_sub_items_table',72);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (224,'2025_09_01_200049_add_column_to_revenue_streams_table',72);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (225,'2025_09_05_143618_add_column_to_otps_table',73);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (226,'2025_09_14_094145_add_column_to_payments_table',74);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (227,'2025_09_17_113533_add_column_to_revenue_streams_table',75);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (228,'2025_09_19_091850_add_column_to_assemblies_table',76);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (229,'2025_10_31_055702_add_active_to_roles_table',77);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (230,'2025_06_20_205428_create_validated_businesses_table',78);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (231,'2025_06_20_205514_create_validated_properties_table',78);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (232,'2025_07_01_085051_create_call_back_requests_table',78);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (233,'2025_07_17_142008_add_authorization_url_to_payments_table',78);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (234,'2025_11_10_064356_add_change_password_to_users_table',79);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (235,'2025_11_14_071209_add_user_id_to_otps_table',80);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (236,'2025_12_07_161711_add_assembly_code_to_revenue_stream_types',81);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (237,'2025_12_07_162810_add_assembly_code_to_property_classes',82);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (238,'2025_12_07_163809_add_assembly_code_to_property_types',83);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (1,'2025_12_17_061306_convert_bill_adjustments_erms_id_to_json',84);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (2,'2026_01_08_151358_create_job_batches_table',84);
SELECT setval('migrations_id_seq', (SELECT MAX("id") FROM "migrations"));


