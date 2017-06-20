CREATE OR REPLACE FUNCTION sproc_read_participation_type_count()
RETURNS bigint AS $$
	SELECT COUNT(*) FROM participation_types AS p WHERE NOT p.is_deleted;
	$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_participation_type_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text) AS $$
	SELECT p.id, p.name FROM participation_types as p WHERE NOT p.is_deleted
	ORDER BY p.id
	LIMIT sproc_read_participation_type_get_subset.lim OFFSET sproc_read_participation_type_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_participation_type_get_all()
RETURNS TABLE(id int, name text) AS $$
	SELECT p.id, p.name FROM participation_types as p WHERE NOT p.is_deleted
	ORDER BY p.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_participation_type_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT p.id, p.name FROM participation_types as p WHERE NOT p.is_deleted
	ORDER BY p.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_participation_type_get(id int)
RETURNS TABLE(id int, name text) AS $$
	SELECT p.id, p.name FROM participation_types AS p WHERE p.id = sproc_read_participation_type_get.id AND NOT p.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_participation_type_create(name text)
RETURNS TABLE(id int) AS $$
	INSERT INTO participation_types AS p (name) VALUES (sproc_write_participation_type_create.name) RETURNING p.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_participation_type_update(id int, name text)
RETURNS VOID AS $$
	UPDATE participation_types AS p
	SET name = sproc_write_participation_type_update.name
	WHERE p.id = sproc_write_participation_type_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_participation_type_delete(id int)
RETURNS VOID AS $$
	UPDATE participation_types AS p
	SET is_deleted = TRUE
	WHERE p.id = sproc_write_participation_type_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
