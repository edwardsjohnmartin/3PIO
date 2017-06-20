CREATE OR REPLACE FUNCTION sproc_read_role_count()
RETURNS bigint AS $$
	SELECT COUNT(*) FROM roles AS r WHERE NOT r.is_deleted;
	$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_role_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text) AS $$
	SELECT r.id, r.name FROM roles as r WHERE NOT r.is_deleted
	ORDER BY r.id-
	LIMIT sproc_read_role_get_subset.lim OFFSET sproc_read_role_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_role_get_all()
RETURNS TABLE(id int, name text) AS $$
	SELECT r.id, r.name FROM roles as r WHERE NOT r.is_deleted
	ORDER BY r.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_role_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT r.id, r.name FROM roles as r WHERE NOT r.is_deleted
	ORDER BY r.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_role_get(id int)
RETURNS TABLE(id int, name text) AS $$
	SELECT r.id, r.name FROM roles AS r WHERE r.id = sproc_read_role_get.id AND NOT r.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_role_create(name text)
RETURNS TABLE(id int) AS $$
	INSERT INTO roles AS r (name) VALUES (sproc_write_role_create.name) RETURNING r.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_role_update(id int, name text)
RETURNS VOID AS $$
	UPDATE roles AS r
	SET name = sproc_write_role_update.name
	WHERE r.id = sproc_write_role_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_role_delete(id int)
RETURNS VOID AS $$
	UPDATE roles AS r
	SET is_deleted = TRUE
	WHERE r.id = sproc_write_role_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
