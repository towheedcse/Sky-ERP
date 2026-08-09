-- Stock Transfer request/accept/reject handshake
-- Adds a destination-acceptance step on top of the existing approve->transfer flow.
-- transfer_request_status: 0 = not requested (source may click Transfer),
--                          1 = requested / awaiting destination acceptance (locked at source),
--                          2 = rejected by destination (back to source with reason, editable/resubmit).
-- Apply once per project DB (oracle, fpp, thai, skyerp). Idempotent-safe columns.

ALTER TABLE pending_stock_transfer_master
    ADD COLUMN transfer_request_status TINYINT(4) NOT NULL DEFAULT 0 AFTER approved_time,
    ADD COLUMN reject_reason VARCHAR(255) NULL AFTER transfer_request_status,
    ADD COLUMN requested_by VARCHAR(30) NULL AFTER reject_reason,
    ADD COLUMN requested_time DATETIME NULL AFTER requested_by,
    ADD COLUMN responded_by VARCHAR(30) NULL AFTER requested_time,
    ADD COLUMN responded_time DATETIME NULL AFTER responded_by;
