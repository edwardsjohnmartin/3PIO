CREATE OR REPLACE FUNCTION sproc_read_course_count()
RETURNS bigint AS $$
	SELECT COUNT(*) FROM courses AS c WHERE NOT c.is_deleted;
	$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_course_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text) AS $$
	SELECT c.id, c.name FROM courses as c WHERE NOT c.is_deleted
	ORDER BY c.id
	LIMIT sproc_read_course_get_subset.lim OFFSET sproc_read_course_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_course_get_all()
RETURNS TABLE(id int, name text) AS $$
	SELECT c.id, c.name FROM courses as c WHERE NOT c.is_deleted
	ORDER BY c.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_course_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT c.id, c.name FROM courses as c WHERE NOT c.is_deleted
	ORDER BY c.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_course_get(id int)
RETURNS TABLE(id int, name text) AS $$
	SELECT c.id, c.name FROM courses AS c WHERE c.id = sproc_read_course_get.id AND NOT c.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_course_create(name text)
RETURNS TABLE(id int) AS $$
	INSERT INTO courses AS c (name) VALUES (sproc_write_course_create.name) RETURNING c.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_course_update(id int, name text)
RETURNS VOID AS $$
	UPDATE courses AS c
	SET name = sproc_write_course_update.name
	WHERE c.id = sproc_write_course_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_course_delete(id int)
RETURNS VOID AS $$
	UPDATE courses AS c
	SET is_deleted = TRUE
	WHERE c.id = sproc_write_course_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
