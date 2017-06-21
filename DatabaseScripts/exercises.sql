-- important! these assume only one starter code file, but the database is designed so that more starter code files can be used later

-- i should limit my inner joins...

CREATE OR REPLACE FUNCTION sproc_read_exercise_count()
RETURNS bigint AS $$
	-- speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM exercises AS e WHERE NOT e.is_deleted; -- i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_exercise_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text, description text, starter_code text, test_code text, language json) AS $$
	SELECT e.id, e.name, e.description, escf.contents AS starter_code, e.test_code, row_to_json(ROW(l.id, l.name)::key_value_pair) AS language
	FROM exercises e
	INNER JOIN languages l ON (e.language_id = l.id)
	INNER JOIN exercise_starter_code_files escf ON (e.id = escf.exercise_id)
	WHERE NOT e.is_deleted
	ORDER BY e.id
	LIMIT sproc_read_exercise_get_subset.lim OFFSET sproc_read_exercise_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

-- i don't think i will ever want to use this! i probably shouldn't allow it.
CREATE OR REPLACE FUNCTION sproc_read_exercise_get_all()
RETURNS TABLE(id int, name text, description text, starter_code text, test_code text, language json, tags json) AS $$
	SELECT e.id, e.name, e.description, escf.contents AS starter_code, e.test_code, row_to_json(ROW(l.id, l.name)::key_value_pair) AS language, array_to_json(array_agg(ROW(t.id, t.name)::key_value_pair)) AS tags
	FROM exercises e
	INNER JOIN languages l ON (e.language_id = l.id)
	INNER JOIN exercise_starter_code_files escf ON (e.id = escf.exercise_id)
	LEFT JOIN tags_to_exercises AS tte ON e.id = tte.exercise_id
	LEFT JOIN tags AS t ON tte.tag_id = t.id
	WHERE NOT e.is_deleted
	GROUP BY e.id, l.id, escf.id
	ORDER BY e.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_exercise_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT e.id, e.name FROM exercises AS e WHERE NOT e.is_deleted
	ORDER BY e.id;
$$ LANGUAGE SQL SECURITY DEFINER;

-- if there are no tags, it gives [{"key":null,"value":null}]. not really ideal.
CREATE OR REPLACE FUNCTION sproc_read_exercise_get(id int)
RETURNS TABLE(id int, name text, description text, starter_code text, test_code text, language json, tags json) AS $$
	SELECT e.id, e.name, e.description, escf.contents AS starter_code, e.test_code, row_to_json(ROW(l.id, l.name)::key_value_pair) AS language, array_to_json(array_agg(ROW(t.id, t.name)::key_value_pair)) AS tags
	FROM exercises e
	INNER JOIN languages l ON (e.language_id = l.id)
	INNER JOIN exercise_starter_code_files escf ON (e.id = escf.exercise_id)
	LEFT JOIN tags_to_exercises AS tte ON e.id = tte.exercise_id
	LEFT JOIN tags AS t ON tte.tag_id = t.id
	WHERE e.id = sproc_read_exercise_get.id AND NOT e.is_deleted
	GROUP BY e.id, l.id, escf.id; -- is this group by stuff correct?
$$ LANGUAGE SQL SECURITY DEFINER;

/*
CREATE OR REPLACE FUNCTION sproc_write_exercise_create(name text, description text, starter_code text, test_code text, language int, tags int[])
RETURNS TABLE(id int) AS $$
	WITH ROWS AS(
	INSERT INTO exercises AS e (name, description, test_code, language_id)
	VALUES (sproc_write_exercise_create.name, sproc_write_exercise_create.description, sproc_write_exercise_create.test_code, sproc_write_exercise_create.language) RETURNING e.id
	)
	INSERT INTO exercise_starter_code_files AS escf (exercise_id, contents)
	SELECT ROWS.id, sproc_write_exercise_create.starter_code FROM ROWS returning escf.exercise_id;

	-- DELETE FROM tags_to_exercises AS tte WHERE tte.exercise_id = ROWS.id;

$$ LANGUAGE SQL SECURITY DEFINER;
*/

-- somewhere something needs to make sure the tag ids are unique. server? db? i think i will depend on server to pass in good values. well.. can do insert and check for that or something...
-- who will make sure that the tag ids are valid
CREATE OR REPLACE FUNCTION sproc_write_exercise_create(name text, description text, starter_code text, test_code text, language int, tags int[])
RETURNS TABLE(id int) AS $$
	DECLARE
		ret_id int;
	BEGIN
	INSERT INTO exercises AS e (name, description, test_code, language_id)
	VALUES (sproc_write_exercise_create.name, sproc_write_exercise_create.description, sproc_write_exercise_create.test_code, sproc_write_exercise_create.language) RETURNING e.id into ret_id;
	INSERT INTO exercise_starter_code_files AS escf (exercise_id, contents)
	VALUES (ret_id, sproc_write_exercise_create.starter_code);
	INSERT INTO tags_to_exercises AS tte (exercise_id, tag_id) VALUES (ret_id, unnest(tags));
	RETURN QUERY SELECT ret_id AS id;
	END
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_exercise_update(id int, name text, description text, starter_code text, test_code text, language int, tags int[])
RETURNS VOID AS $$
UPDATE exercises AS e
	SET name = sproc_write_exercise_update.name,
	description = sproc_write_exercise_update.description,
	test_code = sproc_write_exercise_update.test_code,
	language_id = sproc_write_exercise_update.language
	WHERE e.id = sproc_write_exercise_update.id;
UPDATE exercise_starter_code_files AS escf
	SET contents = sproc_write_exercise_update.starter_code
	WHERE escf.exercise_id = sproc_write_exercise_update.id;
DELETE FROM tags_to_exercises AS tte WHERE sproc_write_exercise_update.id = tte.exercise_id; -- instead, i should do some kind of join... the lessons is where i need to delete and add back because of the order? well but this is simple enough. i'll leave it for now.
INSERT INTO tags_to_exercises AS tte (exercise_id, tag_id) VALUES (sproc_write_exercise_update.id, unnest(tags));
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_exercise_delete(id int) -- should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
	UPDATE exercises AS e
	SET is_deleted = TRUE -- ???
	WHERE e.id = sproc_write_exercise_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
