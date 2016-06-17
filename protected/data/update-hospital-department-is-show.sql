/** 把没有关联过医生的科室的is_show=0 **/
UPDATE hospital_department SET is_show = 0 WHERE id NOT IN (SELECT DISTINCT(hp_dept_id) FROM hospital_dept_doctor_join);

/** 把没有可以显示的科室的医院的is_show=0 **/
UPDATE hospital SET is_show = 0 WHERE id NOT IN (SELECT DISTINCT(hospital_id) FROM hospital_department WHERE is_show=1);