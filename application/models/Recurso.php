<?php

class Application_Model_Recurso extends Zend_Db_Table
{
    protected $_name = 'recurso';
    protected $_primary = 'id';

    const ESTADO_INACTIVO = 0;
    const ESTADO_ACTIVO = 1;
    const ESTADO_ELIMINADO = 2;
    
    const TABLA = 'recurso';
    
    const PADRE = 1;
    
    const FUNCION_LISTADO = 'fetchAll';
      
    public function guardar($datos)
    {         
        $id = 0;
        if (!empty($datos['id'])) {
            $id = (int) $datos['id'];
        }
        unset($datos['id']);

        $datos = array_intersect_key($datos, array_flip($this->_getCols()));

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $id = $this->insert($datos);
        }
        return $id;
        
    }
    
    //Para generar el menú dinámico 
    public function recursoByRol ($rol)
    {   
        return $this->getAdapter()->select()->from(array("a" => $this->_name))
                ->joinInner(array("b" => "rol_recurso"), "b.id_recurso = a.id",null)
                ->where("b.id_rol = ?", $rol)->where("estado = ?",self::ESTADO_ACTIVO)->where("orden  != ?",self::PADRE)
                ->order(array('a.padre asc','a.orden asc'))->query()->fetchAll();
    }
    
    public function obtenerPadre ($key)
    {
        return $this->getAdapter()->select()->from($this->_name, array('padre', 'funcion_listado', 'estado'))
                ->where('access = ?','admin:'.$key)->query()->fetch(Zend_Db::FETCH_NUM);
    }
    
    //Para generar el menú dinámico 
    public function recursosPadre($rol)
    {   
        return $this->getAdapter()->select()->from(array("a" => $this->_name))
                ->joinInner(array("b" => "rol_recurso"), "b.id_recurso = a.id",null)
                ->where("b.id_rol = ?", $rol)->where("estado = ?",self::ESTADO_ACTIVO)
                ->where('a.orden = ?', self::PADRE)
                ->order(array('a.padre asc','a.orden asc'))->query()->fetchAll();
    }
    
    //Para generar el menú dinámico 
    public function recursosHijo($rol, $padre)
    {   
        return $this->getAdapter()->select()->from(array("a" => $this->_name))
                ->joinInner(array("b" => "rol_recurso"), "b.id_recurso = a.id",null)
                ->where("b.id_rol = ?", $rol)->where("estado = ?",self::ESTADO_ACTIVO)
                ->where('a.orden != ?', self::PADRE)
                ->where('a.padre = ?', $padre)
                ->order(array('a.orden asc'))->query()->fetchAll();
    }
   
    //Para generar el menú a SUṔER
    public function listaRecursosSuper ()
    {
        return $this->getAdapter()->select()->distinct()->from(array("a" => $this->_name))
                ->where("estado = ?",self::ESTADO_ACTIVO)->where("orden  != ?",self::PADRE)
                ->order(array('a.padre asc','a.orden asc'))->query()->fetchAll();
    }
    
    
    //Generación de menú
    public function generacionMenu($padre, $active)
    {
         $auth = Zend_Auth::getInstance();
         $rol = $auth->getIdentity()->id_rol;
         
         $dataRecursos = $this->recursosPadre($rol);
         $menu = '';
         
  
         foreach ($dataRecursos as $reg) {
             
             $idPadre = $reg['padre'];
             $dataHijos = $this->recursosHijo($rol, $idPadre);
             
             if (count($dataHijos) > 0) {
                
                $open = '';
                if ($padre == $idPadre) {
                    $open = 'open';
                }
                
                $menu .= '<li class="submenu '.$open.'">';
                $menu .= '<a href="#" title="'.$reg['accion'].'" class="tip-right"><i class="icon icon-th-list"></i>'; 
                $menu .= '<span>'.$reg['nombre'].'</span><span class="label">'.  count($dataHijos).'</span></a>';
                $menu .= '<ul>';
                
                foreach ($dataHijos as $hijo) {
                    //Validar redirección
                    $class = '';
                    if (!empty($active)){
                        if ('admin:'.$active == $hijo['access']){
                            $class = 'class="active"';
                            empty($active);
                        }    
                    }
                    $menu .= '<li '.$class.'><a  href="'.SITE_URL.'/'.$hijo['url'].'" title="'.$hijo['accion'].'" class="tip-right">'.$hijo['nombre'].'</a></li>';
                }
                
                $menu .= '</ul>';
                $menu .= '</il>';
             } else {
                 //El único que no tendra hijos será el INICIO por el momento
                 $menu .= '<li><a href="'.SITE_URL.'/admin"><i class="icon icon-th"></i> <span>'.$reg['nombre'].'</span></a></li>';
                 
             }
             
         }

         $nReg = 0;
         return array("menu" => $menu,"registro" => $nReg);
    }
    
    //Generación de menú2
    public function generacionMenu2()
    {
         $auth = Zend_Auth::getInstance();
         $rol = $auth->getIdentity()->id_rol;
         
         $dataRecursos = $this->recursosPadre($rol);
         $menu = '';
         
  
         foreach ($dataRecursos as $reg) {
             
             $idPadre = $reg['padre'];
             $dataHijos = $this->recursosHijo($rol, $idPadre);
             
             if (count($dataHijos) > 0) {
                
                $menu .= '<li class="btn btn-inverse dropdown">';
                $menu .= '<a href="#" data-toggle="dropdown" class="dropdown-toggle" ><i class="icon icon-th-list"></i>'; 
                $menu .= '<span>'.$reg['nombre'].'</span><span class="label">'.  count($dataHijos).'</span></a>';
                $menu .= '<ul class="dropdown-menu">';
                
                foreach ($dataHijos as $hijo) {
                    $menu .= '<li><a  href="'.SITE_URL.'/'.$hijo['url'].'" title="'.$hijo['accion'].'" class="tip-right">'.$hijo['nombre'].'</a></li>';
                }
                
                $menu .= '</ul>';
                $menu .= '</li>';
             } else {
                 $menu .= '<li class="btn btn-inverse dropdown">';
                 $menu .= '<a href="'.SITE_URL.'/admin"><i class="icon icon-th"></i> <span>'.$reg['nombre'].'</span></a>';
                 $menu .= '</li>';
                 
             }
             
         }

         $nReg = 0;
    
         return array("menu" => $menu,"registro" => $nReg);
    }
    
    


}

