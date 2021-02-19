use prestamo;
DROP FUNCTION IF EXISTS `mora`;
DELIMITER $$

CREATE FUNCTION mora(idPrestamo bigint, idAmortizacion bigint) RETURNS decimal(20, 2)
     READS SQL DATA
DETERMINISTIC
BEGIN
declare vIdEmpresa int;
declare vDiasAtrasados int;
declare vDiasGracia int;
declare vCuota decimal(20, 2);
declare vCapital decimal(20, 2);
declare vInteres decimal(20, 2); 
declare vPorcentajeMora decimal(10, 2); 
declare vCapitalPendiente decimal(20, 2);
declare vMora decimal(20, 2); 
declare vDescripcionTipo varchar(50);
	
    -- Busco los dias atrasados, la cuota, capital e interes de la amortizacion y si los diasAtrasados es < 0 o NULL 
    -- pues no hay dias atrasados asi que retorno 0 de mora
	SELECT DATEDIFF(CURDATE(), a.fecha), a.cuota, a.capital, a.interes FROM amortizations a WHERE a.id = idAmortizacion  into vDiasAtrasados, vCuota, vCapital, vInteres;
    if vDiasAtrasados IS NULL OR vDiasAtrasados < 0 THEN
		return 0;
     end if;
     
     -- OPTENEMOS diasGracia, idEmpresa, capitalPendiente y el porcentaje mora del prestamo y 
     -- si el porcentajeMora = 0 o NULL entonces la mora sera igual a cero
     SELECT l.diasGracia, l.idEmpresa, l.capitalPendiente, l.porcentajeMora FROM loans l WHERE l.id = idPrestamo into vDiasGracia, vIdEmpresa, vCapitalPendiente, vPorcentajeMora;
     IF vPorcentajeMora IS NULL OR vPorcentajeMora = 0 THEN
		RETURN 0;
	 END IF;
     
     -- Retornamos la mora de acuerdo al tipo de mora de la empresa, si es Capital pendiente, Cuota vencida o Capital vencido
     if vDiasAtrasados >= vDiasGracia OR vDiasGracia IS NULL THEN
		SELECT t.descripcion FROM types t where t.id = (SELECT c.idTipoMora FROM companies c WHERE c.id = vIdEmpresa) ORDER BY t.id DESC INTO vDescripcionTipo;
        IF vDescripcionTipo = 'Capital pendiente' THEN
			set @moraToPorciento = ROUND(vPorcentajeMora / 100, 2);
			set vMora = vCapitalPendiente * @moraToPorciento;
		ELSEIF vDescripcionTipo = 'Cuota vencida' THEN
			set @moraToPorciento = ROUND(vPorcentajeMora / 100, 2);
			set vMora = vCuota* @moraToPorciento;
		ELSE 
			set @moraToPorciento = ROUND(vPorcentajeMora / 100, 2);
			set vMora = vCapital* @moraToPorciento;
		END IF;
     END IF;
     
     
	return vMora;
end;

