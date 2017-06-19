--------the read ones should also return the users in the section!!!!!!

CREATE OR REPLACE FUNCTION sproc_read_function_count()
RETURNS bigint AS $$
	--speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM functions AS f WHERE NOT f.is_deleted; --i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_function_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text, code text, section json, "user" json) AS $$
	SELECT f.id, f.name, f.code, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, row_to_json(ROW(u.id, u.name)::key_value_pair) AS "user"
	FROM functions f
	INNER JOIN sections s ON (f.section_id = s.id)
	INNER JOIN users u ON (f.user_id = u.id)
	WHERE NOT f.is_deleted
	ORDER BY f.id
	LIMIT sproc_read_function_get_subset.lim OFFSET sproc_read_function_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

--i don't think i will ever want to use this! i probably shouldn't allow it.
CREATE OR REPLACE FUNCTION sproc_read_function_get_all()
RETURNS TABLE(id int, name text, code text, section json, "user" json) AS $$
	SELECT f.id, f.name, f.code, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, row_to_json(ROW(u.id, u.name)::key_value_pair) AS "user"
	FROM functions f
	INNER JOIN sections s ON (f.section_id = s.id)
	INNER JOIN users u ON (f.user_id = u.id)
	WHERE NOT f.is_deleted
	ORDER BY f.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_function_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT f.id, f.name FROM functions AS f WHERE NOT f.is_deleted
	ORDER BY f.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_function_get(id int)
RETURNS TABLE(id int, name text, code text, section json, "user" json) AS $$
	SELECT f.id, f.name, f.code, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, row_to_json(ROW(u.id, u.name)::key_value_pair) AS "user"
	FROM functions f
	INNER JOIN sections s ON (f.section_id = s.id)
	INNER JOIN users u ON (f.user_id = u.id)
	WHERE f.id = sproc_read_function_get.id AND NOT f.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_function_create(name text, code text, section int, "user" int)
RETURNS TABLE(id int) AS $$
	INSERT INTO functions AS f (name, code, section_id, user_id)
	VALUES (sproc_write_function_create.name, sproc_write_function_create.code, sproc_write_function_create.section, sproc_write_function_create."user") RETURNING f.id
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_function_update(id int, name text, code text, section int, "user" int)
RETURNS VOID AS $$
UPDATE functions AS f
	SET name = sproc_write_function_update.name,
	code = sproc_write_function_update.code,
	section_id = sproc_write_function_update.section,
	user_id = sproc_write_function_update."user"
	WHERE f.id = sproc_write_function_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_function_delete(id int) --should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
	UPDATE functions AS f
	SET is_deleted = TRUE --???
	WHERE f.id = sproc_write_function_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
