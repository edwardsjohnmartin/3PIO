--------the read ones should also return the users in the section!!!!!!

CREATE OR REPLACE FUNCTION sproc_read_lesson_count()
RETURNS bigint AS $$
	--speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM lessons AS l WHERE NOT l.is_deleted; --i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_lesson_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text, description text, owner json, exercises json) AS $$
	SELECT l.id, l.name, l.description, row_to_json(ROW(u.id, u.name)::key_value_pair) AS owner, array_to_json(array_agg(ROW(e.id, e.name)::key_value_pair ORDER BY etl.exercise_number)) AS exercises
	FROM lessons l
	INNER JOIN users u ON (l.owner_id = u.id)
	LEFT JOIN exercises_to_lessons AS etl ON l.id = etl.lesson_id
	LEFT JOIN exercises AS e ON etl.exercise_id = e.id
	WHERE NOT l.is_deleted
	GROUP BY l.id, u.id
	ORDER BY l.id
	LIMIT sproc_read_lesson_get_subset.lim OFFSET sproc_read_lesson_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

--i don't think i will ever want to use this! i probably shouldn't allow it.
/*
CREATE OR REPLACE FUNCTION sproc_read_lesson_get_all()
RETURNS TABLE(id int, name text, description text, owner json, exercises json) AS $$
	SELECT l.id, l.name, l.description, row_to_json(ROW(u.id, u.name)::key_value_pair) AS owner, array_to_json(array_agg(ROW(e.id, e.name)::key_value_pair ORDER BY etl.exercise_number)) AS exercises
	FROM lessons l
	INNER JOIN users u ON (l.owner_id = u.id)
	LEFT JOIN exercises_to_lessons AS etl ON l.id = etl.lesson_id
	LEFT JOIN exercises AS e ON etl.exercise_id = e.id
	WHERE NOT l.is_deleted
	GROUP BY l.id, u.id
	ORDER BY l.id;
$$ LANGUAGE SQL SECURITY DEFINER;
*/

CREATE OR REPLACE FUNCTION sproc_read_lesson_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT l.id, l.name FROM lessons AS l WHERE NOT l.is_deleted
	ORDER BY l.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_lesson_get_pairs_for_owner(owner_id int)
RETURNS SETOF key_value_pair AS $$
	SELECT l.id, l.name FROM lessons AS l WHERE NOT l.is_deleted AND l.owner_id = sproc_read_lesson_get_pairs_for_owner.owner_id
	ORDER BY l.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_lesson_get(id int)
RETURNS TABLE(id int, name text, description text, owner json, exercises json) AS $$
	SELECT l.id, l.name, l.description, row_to_json(ROW(u.id, u.name)::key_value_pair) AS owner, array_to_json(array_agg(ROW(e.id, COALESCE(NULLIF(e.name, ''), e.description))::key_value_pair ORDER BY etl.exercise_number)) AS exercises
	FROM lessons l
	INNER JOIN users u ON (l.owner_id = u.id)
	LEFT JOIN exercises_to_lessons AS etl ON l.id = etl.lesson_id
	LEFT JOIN exercises AS e ON etl.exercise_id = e.id
	WHERE l.id = sproc_read_lesson_get.id AND NOT l.is_deleted
	GROUP BY l.id, u.id;
$$ LANGUAGE SQL SECURITY DEFINER;

-- who will make sure that the exercise ids are valid...
-- these allow an exercise to be associated with multiple lessons, for a pool
/*
CREATE OR REPLACE FUNCTION sproc_write_lesson_create(name text, description text, owner int, exercises int[])
RETURNS TABLE(id int) AS $$
	DECLARE
		ret_id int;
	BEGIN
	INSERT INTO lessons AS l (name, description, owner_id)
	VALUES (sproc_write_lesson_create.name, sproc_write_lesson_create.description, sproc_write_lesson_create.owner) RETURNING l.id into ret_id;
	INSERT INTO exercises_to_lessons AS etl (lesson_id, exercise_id, exercise_number) SELECT ret_id, * FROM unnest(exercises) WITH ORDINALITY;
	RETURN QUERY SELECT ret_id AS id;
	END
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_lesson_update(id int, name text, description text, owner int, exercises int[])
RETURNS VOID AS $$
UPDATE lessons AS l
	SET name = sproc_write_lesson_update.name,
	description = sproc_write_lesson_update.description,
	owner_id = sproc_write_lesson_update.owner
	WHERE l.id = sproc_write_lesson_update.id;
DELETE FROM exercises_to_lessons AS etl WHERE sproc_write_lesson_update.id = etl.lesson_id;
INSERT INTO exercises_to_lessons AS etl (lesson_id, exercise_id, exercise_number) SELECT sproc_write_lesson_update.id, * FROM unnest(exercises) WITH ORDINALITY;
$$ LANGUAGE SQL SECURITY DEFINER;
*/

-- these allow an exercise to be associated with only one lesson
CREATE OR REPLACE FUNCTION sproc_write_lesson_create(name text, description text, owner int)
RETURNS TABLE(id int) AS $$
	DECLARE
		ret_id int;
	BEGIN
	INSERT INTO lessons AS l (name, description, owner_id)
	VALUES (sproc_write_lesson_create.name, sproc_write_lesson_create.description, sproc_write_lesson_create.owner) RETURNING l.id INTO ret_id;
	-- INSERT INTO exercises_to_lessons AS etl (lesson_id, exercise_id, exercise_number) SELECT ret_id, * FROM unnest(exercises) WITH ORDINALITY;
	RETURN QUERY SELECT ret_id AS id;
	END
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_lesson_update(id int, name text, description text, owner int)
RETURNS VOID AS $$
UPDATE lessons AS l
	SET name = sproc_write_lesson_update.name,
	description = sproc_write_lesson_update.description,
	owner_id = sproc_write_lesson_update.owner
	WHERE l.id = sproc_write_lesson_update.id;
-- DELETE FROM exercises_to_lessons AS etl WHERE sproc_write_lesson_update.id = etl.lesson_id;
-- INSERT INTO exercises_to_lessons AS etl (lesson_id, exercise_id, exercise_number) SELECT sproc_write_lesson_update.id, * FROM unnest(exercises) WITH ORDINALITY;
$$ LANGUAGE SQL SECURITY DEFINER;

/*
CREATE OR REPLACE FUNCTION sproc_write_lesson_delete(id int) --should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
	UPDATE lessons AS l
	SET is_deleted = TRUE --???
	WHERE l.id = sproc_write_lesson_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
*/

-- deletes associated exercises
CREATE OR REPLACE FUNCTION sproc_write_lesson_delete(id int)
RETURNS VOID AS $$
UPDATE exercises SET is_deleted = true WHERE id IN (
SELECT e.id FROM lessons AS l
JOIN exercises_to_lessons AS etl ON l.id = etl.lesson_id
JOIN exercises AS e ON e.id = etl.exercise_id
WHERE l.id = sproc_write_lesson_delete.id
);
UPDATE lessons AS l SET is_deleted = true WHERE l.id = sproc_write_lesson_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
