<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Navigation;
use App\Models\SubNavigation;
use Illuminate\Support\Str;



class NavigationController extends Controller
{
    // View all navigations
    public function viewNavigation()
    {
        $navs = Navigation::all();
        return view('admin_pages.navigation.index', compact('navs'));
    }

    public function createNavigation(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $nav = new Navigation();
        $nav->name = $request->name;
        if (empty($request->slug)) {
            $nav->slug = Str::slug($request->name, '-');
        } else {
            $nav->slug = $request->slug; // Use raw input
        }
        $nav->save();
        return redirect()->route('navigation')->with('success', 'Navigation created successfully.');
    }


    public function getNavigation(){
        $navs = Navigation::all();
        return response()->json($navs, 200);
    }


    public function createSubNavigation(Request $request) {

        $request->validate([
            'sub_name' => 'required|string|max:255',
            'navigation_id' => 'required|exists:navigations,id',
        ]);

        $subnavigation = new SubNavigation();
        $subnavigation->sub_name = $request->sub_name;
        $subnavigation->slug = Str::slug($request->sub_name, '-');
        $subnavigation->navigation_id = $request->navigation_id;
        $subnavigation->save();
        return redirect()->route('navigation')->with('success', 'Sub-navigation created successfully.');

    }
    // Delete navigation
    public function deleteNavigation($id)
    {
        $navigation = Navigation::findOrFail($id);
        $navigation->delete();
        return redirect()->route('navigation')->with('success', 'Navigation deleted successfully.');
    }
    // Delete sub-navigation

    // json response for sub-navigation
    public function getSubNavigation($id){
        $subnav = SubNavigation::where('navigation_id', $id)->get();
        return response()->json($subnav, 200);
    }

    public function getNavigationjson(){
        $nav = Navigation::all();
        return response()->json($nav, 200);
    }
    // chage sub-navigation
    public function changeSubNavigation($id, Request $request)
    {
        try {
            $data = $request->json()->all();

            $subnav = SubNavigation::findOrFail($id);

            if (!isset($data['navigation_id'])) {
                return response()->json(['error' => 'navigation_id is missing'], 400);
            }
            $subnav->navigation_id = $data['navigation_id'];
            $subnav->save();
            return response()->json(['message' => 'Sub-navigation updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    public function getAllSubNavigation()
    {
        $subnav = SubNavigation::all();
        return response()->json($subnav, 200);
    }
    //  Delete sub-navigation
    

    public function deleteSubNavigation($id)
    {
        $submenu = SubNavigation::find($id);

        if (!$submenu) {
            return response()->json(['message' => 'Submenu not found.'], 404);
        }

        $submenu->delete();

        return response()->json(['message' => 'Submenu deleted successfully.']);
    }

    // update sub-navigation status
    public function updateSubNavigation($id, Request $request)
    {
        $subnav = SubNavigation::findOrFail($id);

        if ($subnav->published == 1) {
            $subnav->published = 0;
        } else {
            $subnav->published = 1;
        }

        $subnav->save();

        return response()->json(['message' => 'Sub-navigation status updated successfully.'], 200);
    }

}