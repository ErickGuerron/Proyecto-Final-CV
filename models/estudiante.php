<?php

class Estudiante {

    public function agregarEstudiante($datos) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO ESTUDIANTES (NOM_EST, APE_EST, TEL_EST, COR_EST, DIR_EST, FEC_NAC, FEC_CRE) VALUES (:nom, :ape, :tel, :cor, :dir, :fec_nac, :fec_cre)");
        $stmt->execute([
            ':nom' => $datos['NOM_EST'],
            ':ape' => $datos['APE_EST'],
            ':tel' => $datos['TEL_EST'],
            ':cor' => $datos['COR_EST'],
            ':dir' => $datos['DIR_EST'],
            ':fec_nac' => $datos['FEC_NAC'],
            ':fec_cre' => date('Y-m-d H:i:s') // Assuming FEC_CRE is creation date
        ]);
        return $db->lastInsertId();
    }
}