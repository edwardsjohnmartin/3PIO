--------the read ones should also return the users in the section!!!!!!
--should also order by something else (secondary)

CREATE OR REPLACE FUNCTION sproc_read_section_count()
RETURNS bigint AS $$
	--speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM sections AS s WHERE NOT s.is_deleted; --i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_section_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text, course json, teacher json, start_date timestamp, end_date timestamp, users json) AS $$
	SELECT s.id, s.name, row_to_json(ROW(c.id, c.name)::key_value_pair) AS course, row_to_json(ROW(t.id, t.name)::key_value_pair) AS teacher, s.start_date, s.end_date, array_to_json(array_agg(ROW(u.id, u.name)::key_value_pair ORDER BY u.name)) AS users
	FROM sections s
	INNER JOIN courses c ON (s.course_id = c.id)
	INNER JOIN users t ON (s.teacher_id = t.id)
	LEFT JOIN users_to_sections AS uts ON s.id = uts.section_id
	LEFT JOIN users AS u on uts.user_id = u.id
	WHERE NOT s.is_deleted
	GROUP BY s.id, c.id, t.id
	ORDER BY s.start_date
	LIMIT sproc_read_section_get_subset.lim OFFSET sproc_read_section_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

--i don't think i will ever want to use this! i probably shouldn't allow it.
--.......... these need to indicate grader/student... for now, i'll assume student
CREATE OR REPLACE FUNCTION sproc_read_section_get_all()
RETURNS TABLE(id int, name text, course json, teacher json, start_date timestamp, end_date timestamp, users json) AS $$
	SELECT s.id, s.name, row_to_json(ROW(c.id, c.name)::key_value_pair) AS course, row_to_json(ROW(t.id, t.name)::key_value_pair) AS teacher, s.start_date, s.end_date, array_to_json(array_agg(ROW(u.id, u.name)::key_value_pair ORDER BY u.name)) AS users
	FROM sections s
	INNER JOIN courses c ON (s.course_id = c.id)
	INNER JOIN users t ON (s.teacher_id = t.id)
	LEFT JOIN users_to_sections AS uts ON s.id = uts.section_id
	LEFT JOIN users AS u on uts.user_id = u.id
	WHERE NOT s.is_deleted
	GROUP BY s.id, c.id, t.id
	ORDER BY s.start_date;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_section_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT s.id, s.name FROM sections AS s WHERE NOT s.is_deleted
	ORDER BY s.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_section_get(id int)
RETURNS TABLE(id int, name text, course json, teacher json, start_date timestamp, end_date timestamp, users json) AS $$
	SELECT s.id, s.name, row_to_json(ROW(c.id, c.name)::key_value_pair) AS course, row_to_json(ROW(t.id, t.name)::key_value_pair) AS teacher, s.start_date, s.end_date, array_to_json(array_agg(ROW(u.id, u.name)::key_value_pair ORDER BY u.name)) AS users
	FROM sections s
	INNER JOIN courses c ON (s.course_id = c.id)
	INNER JOIN users t ON (s.teacher_id = t.id)
	LEFT JOIN users_to_sections AS uts ON s.id = uts.section_id
	LEFT JOIN users AS u on uts.user_id = u.id
	WHERE s.id = sproc_read_section_get.id AND NOT s.is_deleted
	GROUP BY s.id, c.id, t.id;
$$ LANGUAGE SQL SECURITY DEFINER;

--TODO: HARD CODING STUDENT RIGHT NOW! MUST FIX!
CREATE OR REPLACE FUNCTION sproc_write_section_create(name text, course int, teacher int, start_date timestamp, end_date timestamp, users int[])
RETURNS TABLE(id int) AS $$
	DECLARE
		ret_id int;
	BEGIN
	INSERT INTO sections AS s (name, course_id, teacher_id, start_date, end_date)
	VALUES (sproc_write_section_create.name, sproc_write_section_create.course, sproc_write_section_create.teacher, sproc_write_section_create.start_date, sproc_write_section_create.end_date) RETURNING s.id INTO ret_id;
	INSERT INTO users_to_sections AS utc (section_id, user_id, participation_type_id) SELECT ret_id, *, 1 FROM unnest(users);
	RETURN QUERY SELECT ret_id AS id;
	END
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_section_update(id int, name text, course int, teacher int, start_date timestamp, end_date timestamp, users int[])
RETURNS VOID AS $$
UPDATE sections AS s
	SET name = sproc_write_section_update.name,
	course_id = sproc_write_section_update.course,
	teacher_id = sproc_write_section_update.teacher,
	start_date = sproc_write_section_update.start_date,
	end_date = sproc_write_section_update.end_date
	WHERE s.id = sproc_write_section_update.id;
DELETE FROM users_to_sections AS utc WHERE utc.section_id = sproc_write_section_update.id;
INSERT INTO users_to_sections AS utc (section_id, user_id, participation_type_id) SELECT sproc_write_section_update.id, *, 1 FROM unnest(users);
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_section_delete(id int) --should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
	UPDATE sections AS s
	SET is_deleted = TRUE --???
	WHERE s.id = sproc_write_section_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
