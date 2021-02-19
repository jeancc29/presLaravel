use prestamo;
-- select sum(capital) from paydetails;
use prestamo;
select
a.id,
IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capital,
IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interes,
(SELECT mora(l.id, a.id)) as mora,
a.fecha,
DATEDIFF(CURDATE(), a.fecha) diasAtrasados
from amortizations a
inner join loans l on l.id = a.idPrestamo
left join paydetails pd on pd.idAmortizacion = a.id
group by a.id;


SELECT 
DATEDIFF(CURDATE(),STR_TO_DATE(date, '%m/%d/%Y')) AS days
FROM table1;

use prestamo;
set sql_safe_updates = 0;
delete from amortizations;
delete from loans;
set sql_safe_updates = 1;

