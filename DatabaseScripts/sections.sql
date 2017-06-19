--------the read ones should also return the users in the section!!!!!!
--should also order by something else (secondary)

CREATE OR REPLACE FUNCTION sproc_read_section_count()
RETURNS bigint AS $$
	--speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM sections AS s WHERE NOT s.is_deleted; --i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_section_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text, course json, teacher json, start_date timestamp, end_date timestamp) AS $$
	SELECT s.id, s.name, row_to_json(ROW(c.id, c.name)::key_value_pair) AS course, row_to_json(ROW(u.id, u.name)::key_value_pair) AS teacher, s.start_date, s.end_date
	FROM sections s
	INNER JOIN courses c ON (s.course_id = c.id)
	INNER JOIN users u ON (s.teacher_id = u.id)
	WHERE NOT s.is_deleted
	ORDER BY s.start_date
	LIMIT sproc_read_section_get_subset.lim OFFSET sproc_read_section_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

--i don't think i will ever want to use this! i probably shouldn't allow it.
CREATE OR REPLACE FUNCTION sproc_read_section_get_all()
RETURNS TABLE(id int, name text, course json, teacher json, start_date timestamp, end_date timestamp) AS $$
	SELECT s.id, s.name, row_to_json(ROW(c.id, c.name)::key_value_pair) AS course, row_to_json(ROW(u.id, u.name)::key_value_pair) AS teacher, s.start_date, s.end_date
	FROM sections s
	INNER JOIN courses c ON (s.course_id = c.id)
	INNER JOIN users u ON (s.teacher_id = u.id)
	WHERE NOT s.is_deleted
	ORDER BY s.start_date;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_section_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT s.id, s.name FROM sections AS s WHERE NOT s.is_deleted
	ORDER BY s.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_section_get(id int)
RETURNS TABLE(id int, name text, course json, teacher json, start_date timestamp, end_date timestamp) AS $$
	SELECT s.id, s.name, row_to_json(ROW(c.id, c.name)::key_value_pair) AS course, row_to_json(ROW(u.id, u.name)::key_value_pair) AS teacher, s.start_date, s.end_date
	FROM sections s
	INNER JOIN courses c ON (s.course_id = c.id)
	INNER JOIN users u ON (s.teacher_id = u.id)
	WHERE s.id = sproc_read_section_get.id AND NOT s.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_section_create(name text, course int, teacher int, start_date timestamp, end_date timestamp)
RETURNS TABLE(id int) AS $$
	INSERT INTO sections AS s (name, course_id, teacher_id, start_date, end_date)
	VALUES (sproc_write_section_create.name, sproc_write_section_create.course, sproc_write_section_create.teacher, sproc_write_section_create.start_date, sproc_write_section_create.end_date) RETURNING s.id
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_section_update(id int, name text, course int, teacher int, start_date timestamp, end_date timestamp)
RETURNS VOID AS $$
UPDATE sections AS s
	SET name = sproc_write_section_update.name,
	course_id = sproc_write_section_update.course,
	teacher_id = sproc_write_section_update.teacher,
	start_date = sproc_write_section_update.start_date,
	end_date = sproc_write_section_update.end_date
	WHERE s.id = sproc_write_section_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_section_delete(id int) --should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
	UPDATE sections AS s
	SET is_deleted = TRUE --???
	WHERE s.id = sproc_write_section_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
