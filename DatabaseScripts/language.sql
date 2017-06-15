CREATE OR REPLACE FUNCTION sproc_read_language_count()
RETURNS bigint AS $$
	--speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM languages AS l WHERE NOT l.is_deleted; --i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_language_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text) AS $$
	SELECT l.id, l.name FROM languages as l WHERE NOT l.is_deleted
	ORDER BY l.id
	LIMIT sproc_read_language_get_subset.lim OFFSET sproc_read_language_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_language_get_all()
RETURNS TABLE(id int, name text) AS $$
	SELECT l.id, l.name FROM languages as l WHERE NOT l.is_deleted
	ORDER BY l.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_language_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT l.id, l.name FROM languages as l WHERE NOT l.is_deleted
	ORDER BY l.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_language_get(id int)
RETURNS TABLE(id int, name text) AS $$
	SELECT l.id, l.name FROM languages AS l WHERE l.id = sproc_read_language_get.id AND NOT l.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

 --don't have id as parameter? have it but ignore it? have one for both cases?
CREATE OR REPLACE FUNCTION sproc_write_language_create(name text)
RETURNS TABLE(id int) AS $$ --should i return int instead?
	INSERT INTO languages AS l (name) VALUES (sproc_write_language_create.name) RETURNING l.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_language_update(id int, name text)
RETURNS VOID AS $$
	UPDATE languages AS l
	SET name = sproc_write_language_update.name
	WHERE l.id = sproc_write_language_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_language_delete(id int) --should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
--delete should only mark as deleted. will need another column.
	UPDATE languages AS l
	SET is_deleted = TRUE --???
	WHERE l.id = sproc_write_language_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
