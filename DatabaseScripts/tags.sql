CREATE OR REPLACE FUNCTION sproc_read_tag_count()
RETURNS bigint AS $$
	SELECT COUNT(*) FROM tags AS t WHERE NOT t.is_deleted;
	$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_tag_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text) AS $$
	SELECT t.id, t.name FROM tags as t WHERE NOT t.is_deleted
	ORDER BY t.id
	LIMIT sproc_read_tag_get_subset.lim OFFSET sproc_read_tag_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_tag_get_all()
RETURNS TABLE(id int, name text) AS $$
	SELECT t.id, t.name FROM tags as t WHERE NOT t.is_deleted
	ORDER BY t.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_tag_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT t.id, t.name FROM tags as t WHERE NOT t.is_deleted
	ORDER BY t.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_tag_get(id int)
RETURNS TABLE(id int, name text) AS $$
	SELECT t.id, t.name FROM tags AS t WHERE t.id = sproc_read_tag_get.id AND NOT t.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_tag_create(name text)
RETURNS TABLE(id int) AS $$
	INSERT INTO tags AS t (name) VALUES (sproc_write_tag_create.name) RETURNING t.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_tag_update(id int, name text)
RETURNS VOID AS $$
	UPDATE tags AS t
	SET name = sproc_write_tag_update.name
	WHERE t.id = sproc_write_tag_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_tag_delete(id int)
RETURNS VOID AS $$
	UPDATE tags AS t
	SET is_deleted = TRUE
	WHERE t.id = sproc_write_tag_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
